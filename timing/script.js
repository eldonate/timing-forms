function startTimer() {
    if (!timerRunning) {
        startTime = new Date().getTime();
        timerInterval = setInterval(updateTimer, 10);
        timerRunning = true;
    }
}

function pauseTimer() {
    clearInterval(timerInterval);
    timerRunning = false;
}

function resetTimer() {
    clearInterval(timerInterval);
    timerRunning = false;
    document.getElementById("timer").innerHTML = "00:00:00:00";
}

function updateTimer() {
    var currentTime = new Date().getTime();
    var elapsedTime = currentTime - startTime;

    var hours = Math.floor(elapsedTime / 3600000);
    var minutes = Math.floor((elapsedTime % 3600000) / 60000);
    var seconds = Math.floor((elapsedTime % 60000) / 1000);
    var milliseconds = elapsedTime % 1000;

    document.getElementById("timer").innerHTML = 
        ("0" + hours).slice(-2) + ":" +
        ("0" + minutes).slice(-2) + ":" +
        ("0" + seconds).slice(-2) + ":" +
        ("00" + milliseconds).slice(-3, -1);
}

function getCurrentTime() {
    var now = new Date();
    var time = now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
    document.getElementById("clock").innerHTML = time;
}

function addToTable() {
    var number = document.getElementById("numberInput").value;
    var stopwatchTime = document.getElementById("timer").innerHTML;
    var table = document.getElementById("dataTable");
    var row = table.insertRow(-1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    cell1.innerHTML = number;
    cell2.innerHTML = stopwatchTime;
    document.getElementById("numberInput").value = "";
    document.getElementById("numberInput").focus();
}

var timerRunning = false;
var startTime;
var timerInterval;

window.onload = function() {
    getCurrentTime();
    setInterval(getCurrentTime, 1000);
    document.getElementById("numberInput").focus();
};
