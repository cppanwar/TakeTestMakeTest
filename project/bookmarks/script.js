window.onload= function (){
		var removebutton = document.getElementsByTagName("button");
		var i;
		for (i= 0;i<removebutton.length; i++) {
			removebutton[i].onclick= removeBookmark;
		}
		
	};
	function removeBookmark( eventObj ) {
		var btn = eventObj.target;
		var btype = btn.getAttribute("class");
		var bid = btn.getAttribute("value");
		
		if (window.XMLHttpRequest) 
		{ 
    		bookmarkRequest = new XMLHttpRequest();
		} 
		else if (window.ActiveXObject) 
		{ 
    		bookmarkRequest = new ActiveXObject("Microsoft.XMLHTTP");
		}
		bookmarkRequest.onreadystatechange = function () {
			if (bookmarkRequest.readyState == 4 && bookmarkRequest.status == 200)
			{
				if (bookmarkRequest.responseText.substring(0,1)== "1" )
				{
						//window.location= root+'/bookmarks/bookmarks.php';
						location.reload();		
				}
			}
		};
		bookmarkRequest.open('GET',root+'/bookmarks/removebookmark.php?type='+btype+'&bid='+bid,true);
		bookmarkRequest.send();	
	}