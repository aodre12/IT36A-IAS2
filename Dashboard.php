<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Neo Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f7fc;
    }

    .container {
      display: flex;
    }

    .sidebar {
      width: 250px;
      background-color: #0066ff;
      color: white;
      height: 100vh;
      padding: 20px;
    }

    .sidebar h2 {
      margin-bottom: 30px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar li {
      margin-bottom: 10px;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
    }

    .main {
      flex: 1;
      padding: 30px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }

    .flex {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .column {
      flex: 1;
      min-width: 300px;
    }

    .circle {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: #e3f2fd;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 24px;
      font-weight: bold;
      margin: 0 auto 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 8px 12px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    .bar {
      height: 10px;
      border-radius: 5px;
      margin-bottom: 5px;
    }

    .bar.green { background-color: #4caf50; width: 70%; }
    .bar.red { background-color: #f44336; width: 60%; }
    .bar.blue { background-color: #2196f3; width: 30%; }
    .bar.orange { background-color: #ff9800; width: 90%; }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>Neo</h2>
      <ul>
        <li><strong>Main</strong></li>
        <li><a href="#">📊 Dashboard</a></li>
        <li><a href="#">📱 Apps</a></li>

        <li style="margin-top: 20px;"><strong>Employee</strong></li>
        <li><a href="#">👥 Employees</a></li>
        <li><a href="#">👔 Clients</a></li>
        <li><a href="#">📁 Projects</a></li>
        <li><a href="#">🎫 Tickets</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main">
      <h2>Attendance Dashboard</h2>

      <div class="flex">
        <!-- Timesheet -->
        <div class="card column">
          <h3>Timesheet</h3>
          <p>Punch in at<br><strong>Wed, 11th Mar 2019 10:00 AM</strong></p>
          <div class="circle">3.45 hrs</div>
          <button style="background-color: #0066ff; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Punch Out</button>
          <p style="margin-top: 10px;">BREAK: 1.21 hrs | Overtime: 3 hrs</p>
        </div>

        <!-- Statistics -->
        <div class="card column">
          <h3>Statistics</h3>
          <p>Today: <div class="bar green"></div></p>
          <p>This Week: <div class="bar red"></div></p>
          <p>This Month: <div class="bar blue"></div></p>
          <p>Remaining: <div class="bar orange"></div></p>
          <p>Overtime: <strong>5 hrs</strong></p>
        </div>

        <!-- Activity -->
        <div class="card column">
          <h3>Today Activity</h3>
          <ul style="list-style: none; padding: 0;">
            <li>✅ Punch in at 10:00 AM</li>
            <li>✅ Punch out at 11:00 AM</li>
            <li>✅ Punch in at 1:30 AM</li>
            <li>✅ Punch out at 3:30 AM</li>
            <li>✅ Punch in at 6:20 AM</li>
            <li>✅ Punch out at 7:00 AM</li>
          </ul>
        </div>
      </div>

      <!-- Attendance List -->
      <div class="card">
        <h3>Attendance List</h3>
        <table>
          <thead>
            <tr>
              <th>S. No</th>
              <th>Date</th>
              <th>Punch In</th>
              <th>Punch Out</th>
              <th>Production</th>
              <th>Break</th>
              <th>Overtime</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>1</td><td>19 Feb 2019</td><td>10 AM</td><td>7 PM</td><td>9 hrs</td><td>1 hr</td><td>2 hrs</td></tr>
            <tr><td>2</td><td>20 Feb 2019</td><td>10 AM</td><td>7 PM</td><td>9 hrs</td><td>1 hr</td><td>2 hrs</td></tr>
            <tr><td>3</td><td>21 Feb 2019</td><td>10 AM</td><td>7 PM</td><td>9 hrs</td><td>1 hr</td><td>2 hrs</td></tr>
            <tr><td>4</td><td>22 Feb 2019</td><td>10 AM</td><td>7 PM</td><td>9 hrs</td><td>1 hr</td><td>2 hrs</td></tr>
            <tr><td>5</td><td>23 Feb 2019</td><td>10 AM</td><td>7 PM</td><td>9 hrs</td><td>1 hr</td><td>2 hrs</td></tr>
            <tr><td>6</td><td>24 Feb 2019</td><td>10 AM</td><td>7 PM</td><td>9 hrs</td><td>1 hr</td><td>0 hrs</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Daily Records Placeholder -->
      <div class="card">
        <h3>Daily Records</h3>
        <p>[Insert bar graph or chart library here — e.g., Chart.js]</p>
      </div>
    </div>
  </div>
</body>
</html>
