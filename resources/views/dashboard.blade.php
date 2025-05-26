<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h2 class="text-lg font-semibold mb-6">Welkom bij het beheersysteem van De Gouden Draak</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Kassa -->
                        <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                            <div class="bg-indigo-500 text-white p-4">
                                <h3 class="text-xl font-semibold">Kassa</h3>
                            </div>
                            <div class="p-4 bg-white">
                                <p class="text-gray-700 mb-4">Verwerk bestellingen en voeg verkopen toe.</p>
                                <a href="{{ route('kassa.cashdesk') }}"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    Naar de kassa →
                                </a>
                            </div>
                        </div>

                        <!-- Verkoopoverzicht -->
                        <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                            <div class="bg-green-500 text-white p-4">
                                <h3 class="text-xl font-semibold">Verkoopoverzicht</h3>
                            </div>
                            <div class="p-4 bg-white">
                                <p class="text-gray-700 mb-4">Bekijk verkoopcijfers en omzet.</p>
                                <a href="{{ route('kassa.overview') }}"
                                    class="text-green-600 hover:text-green-800 font-medium">
                                    Bekijk overzicht →
                                </a>
                            </div>
                        </div>

                        <!-- Menu Beheer (alleen voor admins) -->
                        @if (Auth::user()->isAdmin)
                            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                <div class="bg-amber-500 text-white p-4">
                                    <h3 class="text-xl font-semibold">Menu Beheer</h3>
                                </div>
                                <div class="p-4 bg-white">
                                    <p class="text-gray-700 mb-4">Beheer menu items, prijzen en categorieën.</p>
                                    <a href="{{ route('menu.index') }}"
                                        class="text-amber-600 hover:text-amber-800 font-medium">
                                        Menu beheren →
                                    </a>
                                </div>
                            </div>

                            <!-- Tafel Beheer (alleen voor admins) -->
                            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                <div class="bg-purple-500 text-white p-4">
                                    <h3 class="text-xl font-semibold">Tafel Beheer</h3>
                                </div>
                                <div class="p-4 bg-white">
                                    <p class="text-gray-700 mb-4">Beheer tafels, personen en tablet bestellingen.</p>
                                    <a href="{{ route('tables.index') }}"
                                        class="text-purple-600 hover:text-purple-800 font-medium">
                                        Tafels beheren →
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
