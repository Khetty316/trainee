<link href='/fullcalendar/lib/main.css?v=0.1' rel='stylesheet' />
<script src='/fullcalendar/lib/main.js'></script>
<script>
    //https://fullcalendar.io/
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {

            },

            navLinks: false, // can click day/week names to navigate views
            selectable: true,
            editable: true,
            expandRows: false,
            eventStartEditable: false,
            eventBackgroundColor: 'yellow',
            eventClick: function (arg) {
                alert(arg.event.title);
            },
            editable: true,
            dayMaxEvents: true, // allow "more" link when too many events
            events: <?= $data ?>
        });

        calendar.render();
    });
</script>
<style>
    #calendar {
        /*max-width: 1100px;*/
        margin: 0 auto;
    }

    .fc-scrollgrid-sync-table > tbody > tr > td{
        /*height: 60px*/
    }

    div.fc-view-harness.fc-view-harness-active {
        min-height: 520px!important;
    }

    .fc-daygrid-day-number{
        /*padding-bottom:0px!important;*/
    }

</style>
<div id='calendar' class='w-100 p-1 pb-4'></div>
