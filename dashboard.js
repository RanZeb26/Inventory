fetch("sales-data.php")
  .then(res => res.json())
  .then(data => {
    document.getElementById('onlineTotal').innerText = data.totals.online.toLocaleString();
    document.getElementById('offlineTotal').innerText = data.totals.offline.toLocaleString();
    document.getElementById('marketingTotal').innerText = data.totals.marketing.toLocaleString();

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.months,
        datasets: [
          {
            label: 'Online',
            data: data.online,
            borderColor: '#ff007f',
            tension: 0.4,
            fill: false,
            borderWidth: 3
          },
          {
            label: 'Offline',
            data: data.offline,
            borderColor: '#372adb',
            tension: 0.4,
            fill: false,
            borderWidth: 3
          },
          {
            label: 'Marketing',
            data: data.marketing,
            borderColor: '#f9a825',
            tension: 0.4,
            fill: false,
            borderWidth: 3
          }
        ]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { grid: { display: false }},
          y: { beginAtZero: true, grid: { display: false }}
        }
      }
    });
  });

  fetch("earnings-data.php")
  .then(res => res.json())
  .then(data => {
    document.getElementById('currentTotal').innerText = data.summary.currentTotal + " Php";
    document.getElementById('previousTotal').innerText = data.summary.previousTotal + "Php";
    document.getElementById('currentGrowth').innerText = data.summary.growth + "% Since Last Month";

    const ctx = document.getElementById('barChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: 'Current Year',
            data: data.current,
            backgroundColor: '#007bff'
          },
          {
            label: 'Previous Year',
            data: data.previous,
            backgroundColor: '#d1d1d1'
          }
        ]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { display: false }, beginAtZero: true }
        }
      }
    });
  });

  fetch('get_sales_products.php')
  .then(res => res.json())
  .then(data => {
    const labels = data.map(item => item.label);
    const values = data.map(item => item.value);

    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          label: 'Sales',
          data: values,
          backgroundColor: [
            '#560bd0', '#007bff', '#ffc107', '#00cccc', '#cbe0e3',
            '#74de00', '#6610f2', '#e83e8c'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  })
  .catch(err => console.error('Error fetching data:', err));

  async function loadDashboardData() {
  try {
    const response = await fetch('get_dashboard_data.php');
    const data = await response.json();

    // Circle Progress
    var circle = new ProgressBar.Circle('#circleProgress6', {
      color: '#4caf50',
      strokeWidth: 20,
      trailWidth: 3,
      easing: 'easeInOut',
      duration: 1400,
      text: { autoStyleContainer: false },
      from: { color: '#4caf50', width: 3 },
      to: { color: '#4caf50', width: 10 },
      step: function(state, circle) {
        circle.path.setAttribute('stroke', state.color);
        circle.path.setAttribute('stroke-width', state.width);
        circle.setText(Math.round(circle.value() * 100) + '%');
      }
    });
    circle.text.style.fontSize = '1.5rem';
    circle.animate(data.circleProgress); // e.g. 0.75

    // Chart.js
    const ctx = document.getElementById('eventChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Critical', 'Error', 'Warning'],
        datasets: [{
          label: 'Events',
          data: [
            data.eventStats.critical,
            data.eventStats.error,
            data.eventStats.warning
          ],
          backgroundColor: [
            'rgba(255, 0, 0, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(255, 206, 86, 0.7)'
          ],
          borderColor: [
            'rgba(255, 0, 0, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(255, 206, 86, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

  } catch (error) {
    console.error('Dashboard fetch error:', error);
  }
}

loadDashboardData();