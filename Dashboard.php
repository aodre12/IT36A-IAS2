<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <style>
        /* Basic styling for demonstration */
        body {
            font-family: sans-serif;
            margin: 0;
            background-color: #f4f7f6;
        }
        .dashboard-container {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #0a47a3; /* Example blue color */
            color: white;
            height: 100vh;
            padding-top: 20px;
        }
        .sidebar .logo {
            display: flex;
            align-items: center;
            padding: 0 20px;
            margin-bottom: 30px;
            height: 60px;
        }
        .sidebar .logo img {
            vertical-align: middle;
        }
        .sidebar .logo span {
            font-size: 24px;
            font-weight: bold;
            margin-left: 10px;
            vertical-align: middle;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            padding: 10px 20px;
            cursor: pointer;
        }
        .sidebar ul li:hover {
            background-color: #083b8a; /* Darker shade on hover */
        }
        .sidebar ul li.active {
            background-color: #083b8a; /* Active state color */
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        /* Specific styling for the Attendance List table */
        .attendance-table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }
        .attendance-table th,
        .attendance-table td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .attendance-table th {
            font-weight: bold;
            background-color: #f2f2f2;
            color: #333;
        }
        .attendance-table tbody tr:last-child td {
            border-bottom: none;
        }
        .attendance-table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="logo">
                <img src="/mnt/data/dcfb93b1-45dc-4ae0-b3b9-64fe2617b5f7.png" alt="SafeTime Logo" width="40" />
                <span>STHub</span>
            </div>
            <ul>
                <li class="active">Dashboard</li>
                <li>Apps</li>
                <li>Employees</li>
                <li>Clients</li>
                <li>Projects</li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Attendance</h1>
                <div>Dashboard / Attendance</div>
            </div>
            <div class="dashboard-cards">
                <!-- Timesheet Card -->
                <div class="card">
                    <h2>Timesheet <span style="font-size: 0.8em; color: #666;">11 Mar 2019</span></h2>
                    <div style="margin-bottom: 15px;">
                        Punch in at<br />
                        Wed, 11th Mar 2018 10.00 AM
                    </div>
                    <div
                        style="
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            margin-bottom: 15px;
                        "
                    >
                        <!-- Circular Progress Bar Placeholder -->
                        <div
                            style="
                                width: 120px;
                                height: 120px;
                                border: 8px solid #e0e0e0;
                                border-radius: 50%;
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                font-size: 1.5em;
                                font-weight: bold;
                                color: #0a47a3;
                            "
                        >
                            3.45 hrs
                        </div>
                    </div>
                    <button
                        style="
                            background-color: #4caf50;
                            color: white;
                            padding: 10px 20px;
                            border: none;
                            border-radius: 5px;
                            cursor: pointer;
                            font-size: 1em;
                            margin-bottom: 15px;
                            width: 100%;
                        "
                    >
                        Punch Out
                    </button>
                    <div style="display: flex; justify-content: space-around;">
                        <div>
                            BREAK<br />
                            1.21 hrs
                        </div>
                        <div>
                            Overtime<br />
                            3 hrs
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="card">
                    <h2>Statistics</h2>
                    <div style="margin-bottom: 15px;">
                        <div style="margin-bottom: 10px;">Today</div>
                        <div style="display: flex; align-items: center;">
                            <div
                                style="
                                    flex-grow: 1;
                                    height: 8px;
                                    background-color: #e0e0e0;
                                    border-radius: 4px;
                                    margin-right: 10px;
                                "
                            >
                                <div
                                    style="
                                        width: 43.125%;
                                        height: 100%;
                                        background-color: #4caf50;
                                        border-radius: 4px;
                                    "
                                ></div>
                            </div>
                            <div>3.45 / 8 hrs</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <div style="margin-bottom: 10px;">This Week</div>
                        <div style="display: flex; align-items: center;">
                            <div
                                style="
                                    flex-grow: 1;
                                    height: 8px;
                                    background-color: #e0e0e0;
                                    border-radius: 4px;
                                    margin-right: 10px;
                                "
                            >
                                <div
                                    style="
                                        width: 70%;
                                        height: 100%;
                                        background-color: #f44336;
                                        border-radius: 4px;
                                    "
                                ></div>
                            </div>
                            <div>28 / 40 hrs</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <div style="margin-bottom: 10px;">This Month</div>
                        <div style="display: flex; align-items: center;">
                            <div
                                style="
                                    flex-grow: 1;
                                    height: 8px;
                                    background-color: #e0e0e0;
                                    border-radius: 4px;
                                    margin-right: 10px;
                                "
                            >
                                <div
                                    style="
                                        width: 56.25%;
                                        height: 100%;
                                        background-color: #ff9800;
                                        border-radius: 4px;
                                    "
                                ></div>
                            </div>
                            <div>90 / 160 hrs</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <div style="margin-bottom: 10px;">Remaining</div>
                        <div style="display: flex; align-items: center;">
                            <div
                                style="
                                    flex-grow: 1;
                                    height: 8px;
                                    background-color: #e0e0e0;
                                    border-radius: 4px;
                                    margin-right: 10px;
                                "
                            >
                                <div
                                    style="
                                        width: 31.25%;
                                        height: 100%;
                                        background-color: #2196f3;
                                        border-radius: 4px;
                                    "
                                ></div>
                            </div>
                            <div>50 / 160 hrs</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 0;">
                        <div style="margin-bottom: 10px;">Overtime</div>
                        <div style="display: flex; align-items: center;">
                            <div
                                style="
                                    flex-grow: 1;
                                    height: 8px;
                                    background-color: #e0e0e0;
                                    border-radius: 4px;
                                    margin-right: 10px;
                                "
                            >
                                <div
                                    style="
                                        width: 100%;
                                        height: 100%;
                                        background-color: #ffeb3b;
                                        border-radius: 4px;
                                    "
                                ></div>
                            </div>
                            <div>5 hrs</div>
                        </div>
                    </div>
                </div>

                <!-- Today Activity Card -->
                <div class="card">
                    <h2>Today Activity</h2>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <div
                            style="
                                display: flex;
                                align-items: center;
                                margin-bottom: 15px;
                            "
                        >
                            <div
                                style="
                                    width: 12px;
                                    height: 12px;
                                    border: 2px solid #4caf50;
                                    border-radius: 50%;
                                    margin-right: 10px;
                                    flex-shrink: 0;
                                "
                            ></div>
                            <div>
                                Punch in at<br />
                                <span style="color: #666
