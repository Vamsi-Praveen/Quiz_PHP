<div id="tsparticles"></div>
<script>
      document.addEventListener('contextmenu',function(e){
        e.preventDefault();
    })
      document.addEventListener('keydown',function(e){
        if((e.ctrlKey && e.shiftKey && e.key=='I') || e.key=='F12'){
          e.preventDefault();
        }
      })
</script>
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/confetti.js"></script>
</body>
</html>