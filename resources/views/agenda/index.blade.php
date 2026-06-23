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
                height: 'auto',
                contentHeight: 'auto',
                themeSystem: 'standard'
            });

            calendar.render();
        });
    </script>
    
    <style>
        /* Customizing FullCalendar to match a premium CRM aesthetics */
        .fc { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Toolbar */
        .fc .fc-toolbar-title { font-size: 1.5rem; font-weight: 800; color: #0f172a; letter-spacing: -0.025em; }
        .fc .fc-button-primary { 
            background-color: #ffffff; 
            border: 1px solid #e2e8f0; 
            color: #475569; 
            border-radius: 0.75rem; 
            text-transform: capitalize; 
            font-weight: 600;
            padding: 0.5rem 1rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .fc .fc-button-primary:hover { background-color: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
        .fc .fc-button-primary:disabled { background-color: #f1f5f9; border-color: #e2e8f0; color: #94a3b8; }
        .fc .fc-button-active { 
            background-color: #4f46e5 !important; 
            border-color: #4f46e5 !important; 
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2), 0 2px 4px -1px rgba(79, 70, 229, 0.1) !important;
        }
        
        /* Headers and Grid */
        .fc-theme-standard th { background-color: #f8fafc; padding: 0.75rem; font-size: 0.8rem; text-transform: uppercase; color: #64748b; font-weight: 700; border: none; border-bottom: 1px solid #f1f5f9; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9; }
        .fc .fc-daygrid-day-number { color: #334155; font-size: 0.875rem; font-weight: 600; padding: 0.5rem; }
        .fc .fc-day-today { background-color: #fefce8 !important; } /* Soft yellow for today */
        
        /* Events */
        .fc-event { 
            border: none; 
            border-radius: 0.5rem; 
            font-size: 0.75rem; 
            font-weight: 600; 
            padding: 0.25rem 0.5rem; 
            margin: 0.125rem 0.25rem; 
            cursor: pointer; 
            transition: transform 0.2s, box-shadow 0.2s; 
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .fc-event:hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            z-index: 5;
        }
        .fc-h-event .fc-event-main { color: inherit; }
        
        /* List view */
        .fc-list-day-cushion { background-color: #f8fafc !important; }
        .fc-list-event:hover td { background-color: #f1f5f9 !important; }
        
        /* Hide outline on focus */
        .fc .fc-button:focus { box-shadow: none !important; outline: 2px solid #818cf8; outline-offset: 2px; }
    </style>
</x-app-layout>
