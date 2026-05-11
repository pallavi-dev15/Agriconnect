function getWeather() {
    let city = document.getElementById("city").value;

    if (city === "") {
        document.getElementById("error").innerText = "Please enter a city name";
        return;
    }

    const apiKey = "26b173722f50c4b2945a8b10d58fdb51";
    const url = "https://api.openweathermap.org/data/2.5/weather?q=" + city + "&appid=" + apiKey + "&units=metric";

    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            if (data.cod == "404") {
                document.getElementById("error").innerText = "City not found!";
                return;
            }
            document.getElementById("error").innerText = "";
            document.getElementById("temp").innerText = data.main.temp + "°C";
            document.getElementById("humidity").innerText = data.main.humidity + "%";
            document.getElementById("condition").innerText = data.weather[0].main;
            var iconCode = data.weather[0].icon;
            var iconUrl = "https://openweathermap.org/img/wn/" + iconCode + "@2x.png";
            document.getElementById("icon").src = iconUrl;
        }
    };
    xhr.onerror = function() {
        document.getElementById("error").innerText = "Error fetching data";
    };
    xhr.send();
}