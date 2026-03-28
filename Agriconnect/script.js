async function getWeather() {
    let city = document.getElementById("city").value;

    if (city === "") {
        document.getElementById("error").innerText = "Please enter a city name";
        return;
    }

    const apiKey = "26b173722f50c4b2945a8b10d58fdb51";
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

    try {
        let response = await fetch(url);
        let data = await response.json();

        if (data.cod == "404") {
            document.getElementById("error").innerText = "City not found!";
            return;
        }

        document.getElementById("error").innerText = "";

        // Update values
        document.getElementById("temp").innerText = data.main.temp + "°C";
        document.getElementById("humidity").innerText = data.main.humidity + "%";
        document.getElementById("condition").innerText = data.weather[0].main;

        // 🌦️ Weather Icon
        let iconCode = data.weather[0].icon;
        let iconUrl = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;

        document.getElementById("icon").src = iconUrl;

    } catch (error) {
        console.log(error);
        document.getElementById("error").innerText = "Error fetching data";
    }
}