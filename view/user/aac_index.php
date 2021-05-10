<?php
# Loading config data from *.ini-file
$ini = parse_ini_file ('aac_db_config.ini');

# Assigning the ini-values to usable variables
$db_host = $ini['db_host'];
$db_name = $ini['db_name'];
$db_table = $ini['db_table'];
$db_user = $ini['db_user'];
$db_password = $ini['db_password'];

# Prepare a connection to the mySQL database
$connection = new mysqli($db_host, $db_user, $db_password, $db_name);

?>
<!-- start of the HTML part that Google Chart needs -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="../../styles/user.css">
    <title>Document</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <!-- This loads the 'corechart' package. -->
    <script type="text/javascript">
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Time', 'Temperature'],

            <?php

# This query connects to the database and get the last 10 readings
$sql = "SELECT temperature, date FROM $db_table 
		ORDER BY id DESC LIMIT 10";

$result = $connection->query($sql);  

# This while - loop formats and put all the retrieved data into ['timestamp', 'temperature'] way.
	while ($row = $result->fetch_assoc()) {
		$timestamp_rest = substr($row["date"],-8);
		echo "['".$timestamp_rest."',".$row['temperature']."],";
		}
?>
        ]);

        // Curved line
        var options = {
            title: 'Temperature',
            curveType: 'function',
            legend: {
                position: 'bottom'
            }
        };

        // Curved chart
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);

    } // End bracket from drawChart
    </script>

    <!-- The charts below is ony available in the 'bar' package -->
    <script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawBar);

    function drawBar() {
        var data = google.visualization.arrayToDataTable([
            ['Time', 'Temperature'],
            <?php

# This query connects to the database and get the last 10 readings
$sql = "SELECT temperature, date FROM $db_table  
		ORDER BY id DESC LIMIT 10";

$result = $connection->query($sql);  

# This while - loop formats and put all the retrieved data into ['timestamp', 'temperature'] way.
while ($row = $result->fetch_assoc()) {
    $timestamp_rest = substr($row["date"],-8);
    echo "['".$timestamp_rest."',".$row['temperature']."],";
    }
?>
        ]);

        // Bar graph
        var bar_options = {
            title: 'Temperature',
            bar: {
                groupWidth: '95%'
            },
            legend: {
                position: 'bottom'
            }
        };

        var column_options = {
            width: 800,
            legend: {
                position: 'none'
            },
            chart: {
                title: 'Temperature',
                subtitle: ''
            },
            axes: {
                x: {
                    0: {
                        side: 'top',
                        label: 'Temperature'
                    }
                }
            },
            bar: {
                groupWidth: "90%"
            }
        };

        // Bar chart
        var chart = new google.visualization.BarChart(document.getElementById('barchart_values'));
        chart.draw(data, bar_options);

        // Column chart
        var chart = new google.charts.Bar(document.getElementById('top_x_div'));
        chart.draw(data, google.charts.Bar.convertOptions(column_options));

    } // End bracket from drawBar
    </script>
</head>

<body>
    <?php

# Prepare a connection to the mySQL database
$connection = new mysqli($db_host, $db_user, $db_password, $db_name);

# If there are any errors or the connection is not OK
if ($connection->connect_error) {
	die ("Connection error: ".$connection->connect_error);
}
else {
	echo "<p>Connection is OK.</p>"; # For debugging purposes
}

echo "<p>The data that is presented in the different graphs are:</p>";

# Prepare a query to the mySQL database and get a list of the last 10 readings.
# We select only what we need
$sql = "SELECT temperature, date FROM $db_table ORDER BY id DESC LIMIT 10";
$result = $connection->query($sql);

# If we have at least one hit, we'll show it
# Timestamp is formated to only show the last 8 characters
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$timestamp_rest = substr($row["date"],-8);
		echo "Time: ".$timestamp_rest." ";
		echo "Celsius: ".$row['temperature']."<br />";
	}
} else {
	echo "<p>0 result. The ".$db_table." must be empty.</p>";
}

echo "<p>Last line in PHP</p>"; # For debugging purposes
?>

    <div id="curve_chart" style="width: 900px; height: 480px; margin-bottom: 10px;"></div>
    <div id="barchart_values" style="width: 900px; height: 480px; margin-bottom: 10px;"></div>
    <div id="top_x_div" style="width: 900px; height: 480px; margin-bottom: 10px;"></div>
    <p>Last line in html</p> <!-- For debugging -->
</body>

</html>