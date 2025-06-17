<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dagelijkse Verkooprapporten') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Manual Report Generation -->
                    <div class="bg-indigo-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-indigo-800 mb-3">📊 Handmatig Rapport Genereren</h3>
                        <form action="{{ route('reports.daily-sales.generate') }}" method="POST" class="flex gap-4 items-end">
                            @csrf
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Datum</label>
                                <input type="date" name="date" id="date" value="{{ now()->yesterday()->format('Y-m-d') }}"
                                    class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Rapport Genereren
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Automation Info -->
                    <div class="bg-yellow-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-3">🤖 Automatisering</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-yellow-700 mb-2">
                                    <strong>Automatische Generatie:</strong><br>
                                    Elke dag om 06:00 wordt automatisch een rapport gegenereerd voor de vorige dag.
                                </p>
                                <p class="text-sm text-yellow-700">
                                    <strong>Email Notificaties:</strong><br>
                                    Alle admin gebruikers ontvangen automatisch het rapport per email met Excel bijlage.
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-700 mb-2">
                                    <strong>Handmatige Emails:</strong><br>
                                    Klik op de 📧 knop om een rapport handmatig per email te versturen.
                                </p>
                                <p class="text-sm text-yellow-700">
                                    <strong>Excel Downloads:</strong><br>
                                    Download rapporten direct als Excel bestanden voor verdere analyse.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left border-b">Datum</th>
                                    <th class="px-4 py-2 text-right border-b">Totale Omzet</th>
                                    <th class="px-4 py-2 text-right border-b">Aantal Bestellingen</th>
                                    <th class="px-4 py-2 text-center border-b">Status</th>
                                    <th class="px-4 py-2 text-center border-b">Gegenereerd op</th>
                                    <th class="px-4 py-2 text-center border-b">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border-b">
                                            {{ $report->report_date->format('d-m-Y') }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-right">
                                            €{{ number_format($report->total_sales, 2) }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-right">
                                            {{ $report->total_orders }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center">
                                            @if($report->getFileExists())
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Beschikbaar
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Bestand niet gevonden
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border-b text-center">
                                            {{ $report->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('reports.daily-sales.show', $report) }}" 
                                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">
                                                    Bekijken
                                                </a>
                                                @if($report->getFileExists())
                                                    <a href="{{ route('reports.daily-sales.download', $report) }}" 
                                                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-sm">
                                                        Download
                                                    </a>
                                                    <form method="POST" action="{{ route('reports.daily-sales.send-email', $report) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-2 rounded text-sm"
                                                                onclick="return confirm('Weet je zeker dat je dit rapport per email wilt versturen naar alle admins?')">
                                                            📧 Email
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            Nog geen rapporten gegenereerd.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($reports->hasPages())
                        <div class="mt-6">
                            {{ $reports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
