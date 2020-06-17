 var wsUrl = "ws://127.0.0.1:8812";

var websocket = new WebSocket(wsUrl);

//实例对象的onopen属性
websocket.onopen = function(evt) {
  console.log("conected-swoole-success_chart");
}

// 实例化 onmessage
websocket.onmessage = function(evt) {
  push_b(evt.data);
  console.log("ws-server-return-data-chart:" + evt.data);
}

//onclose
websocket.onclose = function(evt) {
  console.log("close");
}
//onerror

websocket.onerror = function(evt, e) {
  console.log("error:" + evt.data);
}


function push_b(data){
	data =JSON.parse(data);
	html ='<div class="comment">';
		html +='<span>'+data.user+'</span>';
		html +='<span>'+data.content+'</span>';
	html +='</div>';
	$('#comments').prepend(html);
}