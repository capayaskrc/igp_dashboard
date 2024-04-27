import './bootstrap';
import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', function() {

    const calendarEl = document.getElementById('calendar');
    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin]
    });

    calendar.render();
});

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


