(function () {
  var primary = localStorage.getItem("primary") || "#4171cb";
  var secondary = localStorage.getItem("secondary") || "#d34d3f";

  window.TokyoAdminConfig = {
    // Theme Primary Color
    primary: primary,
    // theme secondary color
    secondary: secondary,
  };
})();
