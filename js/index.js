$(document).ready(function () {
  if (
    !$("#myCanvas").tagcanvas({
      textColour: "#2596be",
      outlineThickness: 1,
      maxSpeed: 0.07,
      depth: 0.75,
    })
  ) {
    // TagCanvas failed to load
    $("#myCanvasContainer").hide();
  }
  // your other jQuery stuff here...
});
