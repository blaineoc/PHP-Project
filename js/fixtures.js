var user_id = 0;

function updateFixturesList() {
  const formData = new FormData(document.getElementById("sock_form"));

  fetch("show_fixtures.php", {
    // Change to your PHP script
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (response.ok) {
        return response.text(); // or response.json() based on your response type
      }
      throw new Error("Network response was not ok.");
    })
    .then((data) => {
      document.getElementById("fixtures").innerHTML = data; // Display the response
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}