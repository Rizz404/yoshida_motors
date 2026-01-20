<x-layouts.admin title="Dashboard Overview">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Card 1 --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                <div class="p-3 bg-primary-100 rounded-full text-primary-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">Total Vehicles</p>
                    <p class="text-2xl font-bold text-neutral-800">1,257</p>
                </div>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                <div class="p-3 bg-accent-100 rounded-full text-accent-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">Pending Reviews</p>
                    <p class="text-2xl font-bold text-neutral-800">43</p>
                </div>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                <div class="p-3 bg-secondary-100 rounded-full text-secondary-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">Reported Issues</p>
                    <p class="text-2xl font-bold text-neutral-800">5</p>
                </div>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-neutral-800">$45,200</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-200 flex justify-between items-center bg-neutral-50">
            <h3 class="text-lg font-semibold text-neutral-800">Recent Vehicle Submissions</h3>
            <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-800 hover:underline">View
                All</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Car Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                    {{-- Dummy Row 1 --}}
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500 font-bold">
                                    JD</div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-neutral-900">John Doe</div>
                                    <div class="text-sm text-neutral-500">john@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-neutral-900">Toyota Avanza (2018)</div>
                            <div class="text-sm text-neutral-500">B 1234 XYZ</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                            Jan 20, 2026
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-accent-100 text-accent-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#" class="text-primary-600 hover:text-primary-900 mr-3">Review</a>
                        </td>
                    </tr>

                    {{-- Dummy Row 2 --}}
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500 font-bold">
                                    AS</div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-neutral-900">Alice Smith</div>
                                    <div class="text-sm text-neutral-500">alice@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-neutral-900">Honda Jazz (2015)</div>
                            <div class="text-sm text-neutral-500">D 5678 ABC</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                            Jan 19, 2026
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#" class="text-primary-600 hover:text-primary-900 mr-3">Details</a>
                        </td>
                    </tr>

                    {{-- Dummy Row 3 --}}
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500 font-bold">
                                    BS</div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-neutral-900">Bob Stone</div>
                                    <div class="text-sm text-neutral-500">bob@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-neutral-900">Suzuki Carry (2010)</div>
                            <div class="text-sm text-neutral-500">F 9999 GH</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                            Jan 18, 2026
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-secondary-100 text-secondary-800">
                                Rejected
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#" class="text-primary-600 hover:text-primary-900 mr-3">Details</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.admin>
