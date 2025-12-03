<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css" />
  <link rel="stylesheet" href="css/vertical-layout-light/style.css" />
  <link rel="shortcut icon" href="images/favicon.png" />

  <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container-scroller">
    <?php include 'Navbar/nav.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="typcn typcn-cog-outline"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close typcn typcn-delete-outline"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options" id="sidebar-light-theme">
            <div class="img-ss rounded-circle bg-light border me-3"></div>
            Light
          </div>
          <div class="sidebar-bg-options selected" id="sidebar-dark-theme">
            <div class="img-ss rounded-circle bg-dark border me-3"></div>
            Dark
          </div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles bg-success"></div>
            <div class="tiles bg-warning"></div>
            <div class="tiles bg-danger"></div>
            <div class="tiles bg-primary"></div>
            <div class="tiles bg-info"></div>
            <div class="tiles bg-dark"></div>
            <div class="tiles border"></div>
          </div>
        </div>
      </div>

      <?php include 'sidebar.php'; ?>
<!-- Main content -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 mb-4">
              <h1 class="text-center">📅 Event Calendar</h1>
              <div id="calendar" class="card p-3"></div>
            </div>
          </div>
        </div>
           <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="#">randolfh.com</a> 2025</span>
            </div>
          </footer>
      </div>
      <!-- End of main content -->

      <!-- Modal for adding/editing events -->
      <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTitle">Add Event</h5>
              <!-- Close button 
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              -->
            </div>
            <div class="modal-body">
              <input type="hidden" id="eventId">
              <div class="mb-3">
                <label for="eventTitle" class="form-label">Title</label>
                <input type="text" id="eventTitle" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="eventStart" class="form-label">Start Date</label>
                <input type="datetime-local" id="eventStart" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="eventEnd" class="form-label">End Date</label>
                <input type="datetime-local" id="eventEnd" class="form-control" />
              </div>
            </div>
            <div class="modal-footer">
              <button id="deleteEvent" type="button" class="btn btn-danger d-none">Delete</button>
              <button id="saveEvent" type="button" class="btn btn-primary">Save</button>
              <!-- Cancel button 
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FullCalendar JS -->
 <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- JS logic -->
  <script>
    let calendar;
    let selectedEvent;
    let modal;

    document.addEventListener('DOMContentLoaded', function () {
      const calendarEl = document.getElementById('calendar');
      modal = new bootstrap.Modal(document.getElementById('eventModal'));

      calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        editable: true,
        events: 'Calendar_event/events.php',

        select: function (info) {
          document.getElementById("modalTitle").innerText = "Add Event";
          document.getElementById("eventStart").value = info.startStr;
          document.getElementById("eventEnd").value = info.endStr;
          document.getElementById("eventId").value = "";
          document.getElementById("eventTitle").value = "";
          document.getElementById("deleteEvent").classList.add("d-none");
          modal.show();
        },

        eventClick: function (info) {
          selectedEvent = info.event;
          document.getElementById("modalTitle").innerText = "Edit Event";
          document.getElementById("eventId").value = selectedEvent.id;
          document.getElementById("eventTitle").value = selectedEvent.title;
          document.getElementById("eventStart").value = selectedEvent.start.toISOString().slice(0, 16);
          if (selectedEvent.end) {
            document.getElementById("eventEnd").value = selectedEvent.end.toISOString().slice(0, 16);
          }
          document.getElementById("deleteEvent").classList.remove("d-none");
          modal.show();
        },

        eventDrop: function (info) {
          updateEvent(info.event);
        }
      });

      calendar.render();
    });

    document.getElementById("saveEvent").addEventListener("click", function () {
      const id = document.getElementById("eventId").value;
      const title = document.getElementById("eventTitle").value;
      const start = document.getElementById("eventStart").value;
      const end = document.getElementById("eventEnd").value;

      if (!title || !start || !end) {
        alert("Please fill in all fields.");
        return;
      }

      const url = id ? 'Calendar_event/update_event.php' : 'Calendar_event/add_event.php';
      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, title, start, end })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          calendar.refetchEvents();
          modal.hide();
        } else {
          alert("Failed to save event.");
        }
      });
    });

    document.getElementById("deleteEvent").addEventListener("click", function () {
      const id = document.getElementById("eventId").value;
      if (confirm("Are you sure you want to delete this event?")) {
        fetch('Calendar_event/delete_event.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            calendar.refetchEvents();
            modal.hide();
          } else {
            alert("Failed to delete event.");
          }
        });
      }
    });

    function updateEvent(event) {
      fetch('Calendar_event/update_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: event.id,
          title: event.title,
          start: event.start.toISOString(),
          end: event.end ? event.end.toISOString() : null
        })
      });
    }
  </script>

  <!-- Vendor JS -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
</body>
</html>
