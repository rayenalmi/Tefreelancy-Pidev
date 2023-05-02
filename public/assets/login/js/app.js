document.getElementById("btnpop").addEventListener("click", function() {
    document.getElementById("myPopup").classList.add("show");
  });
  
  // Close the pop-up when the close button is clicked
  document.getElementById("closeButton").addEventListener("click", function() {
    document.getElementById("myPopup").classList.remove("show");
  });


  document.getElementById("goTorecruter").addEventListener("click", function() {
    document.getElementById("myPopup").classList.remove("show");
  });

  document.getElementById("goTofreelancer").addEventListener("click", function() {
    document.getElementById("myPopup").classList.remove("show");
  });