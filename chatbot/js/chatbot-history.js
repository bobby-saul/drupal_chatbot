window.onload = function(){
	var textlog = document.getElementById("chatbothistory")
	textlog.scrollTop = textlog.scrollHeight;
	console.log('scrolled down');
}