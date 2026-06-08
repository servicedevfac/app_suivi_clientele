<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Agenda</h1>
        <p class="text-sm text-slate-500 mt-1">Consultez vos tâches et vos relances planifiées sur le calendrier.</p>
    </x-slot>

    <!-- FullCalendar JS/CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js'></script>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
        <div class="flex justify-end space-x-4 mb-4">
            <div class="flex items-center text-xs">
                <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span> Tâche (Haute)
            </div>
            <div class="flex items-center text-xs">
                <span class="w-3 h-3 rounded-full bg-amber-500 mr-2"></span> Tâche (Moyenne)
            </div>
            <div class="flex items-center text-xs">
                <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span> Tâche (Basse)
            </div>
            <div class="flex items-center text-xs">
                <span class="w-3 h-3 rounded-full bg-violet-500 mr-2"></span> Relance (En attente)
            </div>
            <div class="flex items-center text-xs">
                <span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span> Relance (Effectuée)
            </div>
        </div>
        
        <div id='calendar' class="w-full text-slate-800"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var events = @json($events);

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: events,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                eventClick: function(info) {
                    if(info.event.url) {
                        info.jsEvent.preventDefault(); // don't let the browser navigate
                        window.open(info.event.url, '_blank'); // open in new tab
                    }
                },
                height: 700,
                contentHeight: 650,
                themeSystem: 'standard'
            });

            calendar.render();
        });
    </script>
    
    <style>
        /* Customizing FullCalendar to match CRM aesthetics */
        .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
        .fc .fc-button-primary { background-color: #4f46e5; border-color: #4f46e5; border-radius: 0.5rem; text-transform: capitalize; }
        .fc .fc-button-primary:hover { background-color: #4338ca; border-color: #4338ca; }
        .fc .fc-button-primary:disabled { background-color: #c7d2fe; border-color: #c7d2fe; }
        .fc .fc-button-active { background-color: #3730a3 !important; border-color: #3730a3 !important; }
        .fc-theme-standard th { background-color: #f8fafc; padding: 0.5rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600; border-color: #f1f5f9; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9; }
        .fc .fc-daygrid-day-number { color: #475569; font-size: 0.875rem; font-weight: 500; }
        .fc-event { border: none; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.25rem; margin-bottom: 0.125rem; cursor: pointer; transition: opacity 0.2s; }
        .fc-event:hover { opacity: 0.9; }
        .fc-h-event .fc-event-main { color: inherit; }
    </style>
</x-app-layout>
