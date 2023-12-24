console.log('Chart code is running');

google.charts.load('current', { 'packages': ['corechart'] });
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Date');
    data.addColumn('number', 'Predicted Amount');

    // Fetch prediction data from predict_expenses.php
    fetch('predict.php') // Update the URL to your PHP file
        .then(response => response.json())
        .then(predictions => {
            console.log('Predictions data:', predictions);

            predictions.forEach(function (row) {
                data.addRow([new Date(row.date), row.predicted_amount]);
            });

            var options = {
                title: 'Expense Prediction',
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        });
}
