<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg lg:text-xl text-gray-800 leading-tight">
            {{ __('Prediction History') }}
        </h2>
    </x-slot>

    <div class="py-4 lg:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 lg:mb-6">
                <div class="p-4 lg:p-6">
                    <form method="GET" action="{{ route('admin.predictions.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 lg:gap-4">
                        <div>
                            <label for="crop" class="block text-xs lg:text-sm font-medium text-gray-700">Crop</label>
                            <select id="crop" name="crop" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm lg:text-base">
                                <option value="">All Crops</option>
                                @foreach($crops as $crop)
                                    <option value="{{ $crop }}" {{ request('crop') == $crop ? 'selected' : '' }}>
                                        {{ $crop }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="municipality" class="block text-xs lg:text-sm font-medium text-gray-700">Municipality</label>
                            <select id="municipality" name="municipality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm lg:text-base">
                                <option value="">All Municipalities</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" {{ request('municipality') == $municipality ? 'selected' : '' }}>
                                        {{ $municipality }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-xs lg:text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm lg:text-base">
                                <option value="">All Status</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>

                        <div>
                            <label for="prediction_type" class="block text-xs lg:text-sm font-medium text-gray-700">Type</label>
                            <select id="prediction_type" name="prediction_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm lg:text-base">
                                <option value="">All Types</option>
                                <option value="regular" {{ request('prediction_type') == 'regular' ? 'selected' : '' }}>Regular Prediction</option>
                                <option value="forecast" {{ request('prediction_type') == 'forecast' ? 'selected' : '' }}>Forecast</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block text-xs lg:text-sm font-medium text-gray-700">From Date</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" 
                                placeholder="mm/dd/yyyy"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm lg:text-base">
                        </div>

                        <div>
                            <label for="date_to" class="block text-xs lg:text-sm font-medium text-gray-700">To Date</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" 
                                placeholder="mm/dd/yyyy"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm lg:text-base">
                        </div>

                        <div class="sm:col-span-2 lg:col-span-6 flex flex-col sm:flex-row gap-2">
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                            <button type="button" onclick="window.location.href='{{ route('admin.predictions.history') }}'" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear
                            </button>
                            <a href="{{ route('admin.predictions.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Prediction
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 lg:p-6">
                    @if($predictions->count() > 0)
                        <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <table class="min-w-full divide-y divide-gray-200 text-sm lg:text-base">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crop</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Municipality</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Farm Type</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predicted (MT)</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Difference</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Confidence</th>
                                        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($predictions as $prediction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm text-gray-900">
                                                <div class="lg:hidden">{{ $prediction->created_at->format('M d, Y') }}</div>
                                                <div class="hidden lg:block">{{ $prediction->created_at->format('M d, Y H:i') }}</div>
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm">
                                                @if($prediction->farm_type === 'Forecast')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                                        </svg>
                                                        Forecast
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Regular
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm font-medium text-gray-900">
                                                {{ $prediction->crop }}
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm text-gray-600 hidden sm:table-cell">
                                                {{ $prediction->municipality }}
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm text-gray-600 hidden md:table-cell">
                                                {{ $prediction->farm_type }}
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm font-semibold text-indigo-600">
                                                {{ number_format($prediction->predicted_production_mt, 2) }}
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm hidden lg:table-cell">
                                                <span class="{{ $prediction->difference >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                                    {{ $prediction->difference >= 0 ? '+' : '' }}{{ number_format($prediction->difference, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-xs lg:text-sm text-gray-600 hidden lg:table-cell">
                                                {{ number_format($prediction->confidence_score, 4) }}
                                            </td>
                                            <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap">
                                                @if($prediction->status === 'success')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Success
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Failed
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 lg:mt-6">
                            {{ $predictions->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 lg:py-12">
                            <svg class="mx-auto h-10 w-10 lg:h-12 lg:w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm lg:text-base font-medium text-gray-900">No predictions found</h3>
                            <p class="mt-1 text-xs lg:text-sm text-gray-500">Get started by creating your first prediction.</p>
                            <div class="mt-4 lg:mt-6">
                                <a href="{{ route('admin.predictions.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Create Prediction
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
