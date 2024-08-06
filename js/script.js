const scrollBtn = document.getElementById('scrollToTop');

function scrollToTop(){
	window.scrollTo(0,0)
}

window.onscroll = function(){
	if(document.body.scrollTop > 60 || document.documentElement.scrollTop>60){
		scrollBtn.classList.remove('hidden');
		scrollBtn.classList.add('flex');
	}
	else
	{
		scrollBtn.classList.add('hidden');
	}
}