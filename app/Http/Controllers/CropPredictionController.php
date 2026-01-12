<?php

namespace App\Http\Controllers;

use App\Services\CropPredictionService;
use App\Models\CropProduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CropPredictionController extends Controller
{
    protected $predictionService;
    
    public function __construct(CropPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }
    
    /**
     * Show the prediction form
     */
    public function index()
    {
        try {
            $options = $this->predictionService->getAvailableOptions();
            
            // Determine view based on user role
            $view = auth()->user()->isAdmin() 
                ? 'admin.predictions.index' 
                : 'farmers.predictions.index';
            
            return view($view, compact('options'));
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to load prediction form: ' . $e->getMessage());
        }
    }
    
    /**
     * Make a prediction
     */
    public function predict(Request $request)
    {
        // NEW MODEL: Only 6 features required (no area_harvested or productivity)
        $validator = Validator::make($request->all(), [
            'municipality' => 'required|string',
            'farm_type' => 'required|string',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'crop' => 'required|string',
            'area_planted' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Make prediction via ML API
            $result = $this->predictionService->predict($request->all());
            
            // Extract prediction data
            $prediction = $result['prediction'] ?? $result;
            
            // Save prediction to database (new model - no area_harvested or productivity)
            try {
                $predictionRecord = \App\Models\Prediction::create([
                    'user_id' => auth()->id(),
                    'municipality' => $request->municipality,
                    'farm_type' => $request->farm_type,
                    'year' => $request->year,
                    'month' => $request->month,
                    'crop' => $request->crop,
                    'area_planted_ha' => $request->area_planted,
                    'area_harvested_ha' => null, // New model doesn't use this
                    'productivity_mt_ha' => null, // New model doesn't use this
                    'predicted_production_mt' => $prediction['production_mt'] ?? $prediction['Production_mt'] ?? $prediction['predicted_production'] ?? 0,
                    'expected_from_productivity' => null, // Not applicable for new model
                    'difference' => null, // Not applicable for new model
                    'confidence_score' => $prediction['confidence_score'] ?? $prediction['Confidence_Score'] ?? 0,
                    'api_response_time_ms' => $result['api_response_time_ms'] ?? null,
                    'status' => 'success'
                ]);
                
                // Add prediction ID and success message to result
                $result['prediction_id'] = $predictionRecord->id;
                $result['saved_to_history'] = true;
                
                Log::info('Prediction saved to history', [
                    'prediction_id' => $predictionRecord->id,
                    'user_id' => auth()->id(),
                    'crop' => $request->crop,
                    'municipality' => $request->municipality
                ]);
            } catch (\Exception $dbError) {
                // Log database error but still return prediction result
                Log::error('Failed to save prediction to history database', [
                    'user_id' => auth()->id(),
                    'error' => $dbError->getMessage(),
                    'trace' => $dbError->getTraceAsString()
                ]);
                
                $result['saved_to_history'] = false;
                $result['save_error'] = 'Prediction was successful but could not be saved to history: ' . $dbError->getMessage();
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            // ML API call failed - Save failed prediction to database
            Log::error('Prediction API call failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->only(['municipality', 'farm_type', 'year', 'month', 'crop', 'area_planted'])
            ]);
            
            try {
                \App\Models\Prediction::create([
                    'user_id' => auth()->id(),
                    'municipality' => $request->municipality,
                    'farm_type' => $request->farm_type,
                    'year' => $request->year,
                    'month' => $request->month,
                    'crop' => $request->crop,
                    'area_planted_ha' => $request->area_planted,
                    'area_harvested_ha' => null,
                    'productivity_mt_ha' => null,
                    'predicted_production_mt' => 0,
                    'expected_from_productivity' => null,
                    'difference' => null,
                    'confidence_score' => 0,
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            } catch (\Exception $dbError) {
                Log::error('Failed to save prediction error to database', [
                    'user_id' => auth()->id(),
                    'original_error' => $e->getMessage(),
                    'db_error' => $dbError->getMessage()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get available options for AJAX
     */
    public function getOptions()
    {
        try {
            $options = $this->predictionService->getAvailableOptions();
            return response()->json($options);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show prediction history for the authenticated user
     */
    public function history(Request $request)
    {
        $query = \App\Models\Prediction::forUser(auth()->id())
            ->latest();

        // Apply filters
        if ($request->filled('crop')) {
            $query->byCrop($request->crop);
        }

        if ($request->filled('municipality')) {
            $query->byMunicipality($request->municipality);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('prediction_type')) {
            if ($request->prediction_type === 'forecast') {
                $query->where('farm_type', 'Forecast');
            } elseif ($request->prediction_type === 'regular') {
                $query->where('farm_type', '!=', 'Forecast');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $predictions = $query->paginate(20)->appends($request->query());

        // Get unique values for filters
        $crops = \App\Models\Prediction::forUser(auth()->id())
            ->select('crop')
            ->distinct()
            ->pluck('crop');

        $municipalities = \App\Models\Prediction::forUser(auth()->id())
            ->select('municipality')
            ->distinct()
            ->pluck('municipality');

        // Determine view based on user role
        $view = auth()->user()->isAdmin() 
            ? 'admin.predictions.history' 
            : 'farmers.predictions.history';

        return view($view, compact('predictions', 'crops', 'municipalities'));
    }

    /**
     * Process batch predictions via queue
     */
    public function batchPredict(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'predictions' => 'required|array|min:1|max:100',
            'predictions.*.municipality' => 'required|string',
            'predictions.*.farm_type' => 'required|string',
            'predictions.*.year' => 'required|integer|min:2020|max:2030',
            'predictions.*.month' => 'required|integer|min:1|max:12',
            'predictions.*.crop' => 'required|string',
            'predictions.*.area_planted' => 'required|numeric|min:0',
            'predictions.*.area_harvested' => 'required|numeric|min:0',
            'predictions.*.productivity' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Dispatch batch prediction job
        \App\Jobs\ProcessBatchPrediction::dispatch(auth()->id(), $request->predictions);

        return response()->json([
            'success' => true,
            'message' => 'Batch prediction job queued successfully. You will see results in your prediction history.'
        ]);
    }
    
    /**
     * Generate multi-year forecast
     */
    public function forecast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'municipality' => 'required|string',
            'crop' => 'required|string',
            'farm_type' => 'nullable|string',
            'forecast_years' => 'required|integer|min:1|max:10'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Make forecast via ML API
            $result = $this->predictionService->forecast($request->all());
            
            // Debug: Log the full forecast result structure
            Log::info('Forecast API result received', [
                'has_forecast' => isset($result['forecast']),
                'forecast_count' => isset($result['forecast']) ? count($result['forecast']) : 0,
                'forecast_data' => $result['forecast'] ?? null,
                'metadata' => $result['metadata'] ?? null
            ]);
            
            // Save each forecast year to prediction history
            $savedCount = 0;
            $forecastIds = [];
            $saveErrors = [];
            
            if (isset($result['forecast']) && is_array($result['forecast'])) {
                Log::info('Starting to save forecast predictions', [
                    'user_id' => auth()->id(),
                    'municipality' => $request->municipality,
                    'crop' => $request->crop,
                    'count' => count($result['forecast'])
                ]);
                
                foreach ($result['forecast'] as $index => $forecastYear) {
                    try {
                        // Extract year and production from forecast data
                        // The API returns: {year: 2025, production: 123.45, ...}
                        $year = $forecastYear['year'] ?? $forecastYear['Year'] ?? null;
                        $production = $forecastYear['production'] ?? $forecastYear['Production'] ?? 
                                     $forecastYear['predicted_production'] ?? $forecastYear['Predicted_Production'] ?? 0;
                        
                        // Default to January if month not specified
                        $month = $forecastYear['month'] ?? $forecastYear['Month'] ?? 1;
                        
                        Log::debug('Processing forecast item', [
                            'index' => $index,
                            'year' => $year,
                            'production' => $production,
                            'month' => $month,
                            'raw_data' => $forecastYear
                        ]);
                        
                        if ($year) {
                            $predictionRecord = \App\Models\Prediction::create([
                                'user_id' => auth()->id(),
                                'municipality' => $request->municipality,
                                'farm_type' => $request->farm_type ?? 'Forecast',
                                'year' => $year,
                                'month' => $month,
                                'crop' => $request->crop,
                                'area_planted_ha' => null, // Forecast doesn't specify area
                                'area_harvested_ha' => null,
                                'productivity_mt_ha' => null,
                                'predicted_production_mt' => $production,
                                'expected_from_productivity' => null,
                                'difference' => null,
                                'confidence_score' => 0, // Forecast doesn't provide confidence
                                'api_response_time_ms' => $result['metadata']['api_response_time_ms'] ?? null,
                                'status' => 'success'
                            ]);
                            
                            $forecastIds[] = $predictionRecord->id;
                            $savedCount++;
                            
                            Log::info('Successfully saved forecast year to history', [
                                'prediction_id' => $predictionRecord->id,
                                'year' => $year,
                                'production' => $production
                            ]);
                        } else {
                            Log::warning('Skipped forecast item - no year found', [
                                'index' => $index,
                                'data' => $forecastYear
                            ]);
                        }
                    } catch (\Exception $dbError) {
                        $saveErrors[] = [
                            'year' => $year ?? 'unknown',
                            'error' => $dbError->getMessage()
                        ];
                        
                        Log::error('Failed to save forecast year to history', [
                            'user_id' => auth()->id(),
                            'year' => $year ?? 'unknown',
                            'error' => $dbError->getMessage(),
                            'trace' => $dbError->getTraceAsString(),
                            'data' => $forecastYear ?? null
                        ]);
                    }
                }
            } else {
                Log::warning('No forecast data found in result', [
                    'result_keys' => array_keys($result),
                    'result' => $result
                ]);
            }
            
            // Add save information to result
            $result['saved_to_history'] = $savedCount > 0;
            $result['saved_count'] = $savedCount;
            $result['forecast_ids'] = $forecastIds;
            
            if (!empty($saveErrors)) {
                $result['save_errors'] = $saveErrors;
            }
            
            Log::info('Forecast save complete', [
                'saved_count' => $savedCount,
                'total_items' => isset($result['forecast']) ? count($result['forecast']) : 0,
                'errors' => count($saveErrors)
            ]);
            
            if ($savedCount > 0) {
                Log::info('Forecast saved to history', [
                    'user_id' => auth()->id(),
                    'crop' => $request->crop,
                    'municipality' => $request->municipality,
                    'saved_count' => $savedCount
                ]);
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Forecast generation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->only(['municipality', 'crop', 'farm_type', 'forecast_years'])
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get historical production data for comparison chart
     */
    public function historical(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'municipality' => 'required|string',
            'crop' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Fetch historical data from 2015 to 2024 (aggregated by year)
            $historicalData = CropProduction::where('municipality', $request->municipality)
                ->where('crop', $request->crop)
                ->whereBetween('year', [2015, 2024])
                ->select('year', DB::raw('SUM(production) as production'))
                ->groupBy('year')
                ->orderBy('year')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $historicalData,
                'municipality' => $request->municipality,
                'crop' => $request->crop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}