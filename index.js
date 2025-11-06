document.addEventListener('DOMContentLoaded', (event) => {
    console.log("Loaded!")
    document.getElementById("getStarted").addEventListener('click', (event) => {
        window.location.href = "https://"
        document.innerHTML = "You should have been redirected. Click <a href=\"https://google.com\">here</a> to continue"
    })
})