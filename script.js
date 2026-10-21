document.addEventListener("DOMContentLoaded", () => {
  const button = document.getElementById("get-location");
  if (!button) return;
  const status = document.getElementById("location-status");
  button.addEventListener("click", () => {
    if (!navigator.geolocation) {
      status.textContent = "Geolocation is not supported by this browser.";
      return;
    }
    status.textContent = "Requesting permission…";
    button.disabled = true;
    navigator.geolocation.getCurrentPosition(
      position => {
        document.getElementById("latitude").value = position.coords.latitude.toFixed(7);
        document.getElementById("longitude").value = position.coords.longitude.toFixed(7);
        status.textContent = "Location added. You can remove it by refreshing this page.";
        button.textContent = "Location added ✓";
        button.disabled = false;
      },
      () => {
        status.textContent = "Location unavailable or permission denied. You can still save the record.";
        button.disabled = false;
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
    );
  });
});