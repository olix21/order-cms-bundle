google.load("visualization", "1", {packages:["corechart", "geochart"]});
google.setOnLoadCallback(drawChart);

var months = {month1: null, month2: null, month3: null, month4: null, month5: null};
var currentDate = new Date();

var options = { month: 'long' };

months['month0'] = new Intl.DateTimeFormat('fr', options).format(currentDate);

for(var i=1; i<5; i++)
    months['month'+i] = new Intl.DateTimeFormat('fr', options).format(currentDate.setMonth(currentDate.getMonth() - 1));

function drawChart() {
    var data = google.visualization.arrayToDataTable(stats);

    var options = {
        title: 'En bref',
        width: '100%',
        height: 300,
        curveType: 'function',
        hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
        vAxis: {viewWindow: {min: 0}}
    };

    var chart = new google.visualization.AreaChart(document.getElementById('order_stat_general'));
    chart.draw(data, options);
}


//google.setOnLoadCallback(drawRegionsMap);

/*
function drawRegionsMap() {

    var data = google.visualization.arrayToDataTable([
        ['Country', 'Popularity'],
        ['BE',      5],
        ['FR',      1]
    ]);

    var options = {};

    var chart = new google.visualization.GeoChart(document.getElementById('map'));

    chart.draw(data, options);
}
*/
