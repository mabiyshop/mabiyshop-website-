<template>
<div> 


  <div class="countdown_wrapper">
      <div class="single_timer"> <span>{{time.time}} {{time.text}}</span> </div>
	
	
  </div>
  
</div>
</template>




<script>

export default {
	props: ["time"]
}
</script>