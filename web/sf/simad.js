
function openSecondsTimedWindow ( sbUrl, nuSeconds )
{
  	// ** START **
	imageWin = window.open(sbUrl,'internalview','width=800,height=600,top=0,left=0,menu=no,scrollbars=1,statusbar=1');
	//tres horas>  1000 (sec) * 60 (min) * 60 (hour) * 4 
	//30 segundos= 1/2 Minuto.
	var maxtime=60000;

	if(nuSeconds==0) nuSeconds=maxtime;

	setTimeout("imageWin.close()", nuSeconds*1000 );  

}


function simad_openDialog ( sbUrl, nuSeconds )
{
  	// ** START **
	var r;
	var randomName;
	r=Math.floor(Math.random() * 100); 
	randomName='simadDialog'+r;
	//alert(randomName);
	imageWin = window.open(sbUrl,randomName,'width=400,height=300,top=0,left=0,menu=no,scrollbars=0,statusbar=1');
	//tres horas>  1000 (sec) * 60 (min) * 60 (hour) * 4 
	//30 segundos= 1/2 Minuto.
	var maxtime=60000;

	if(nuSeconds==0) nuSeconds=maxtime;

	setTimeout("imageWin.close()", nuSeconds*1000 );  

}

function simad_openSizedWindow(sbUrl,nuSeconds,nuSize)
{
  	// ** START **
	var r;
	var randomName;

	var w=320;
	var h=240;
	var scale=1;

	r=Math.floor(Math.random() * 100); 
	randomName='simadDialog'+r;
	//alert(randomName);


	//nusize is an scale factor:
	if(nuSize>0 && nuSize<4){
		w=nuSize*w;
		h=nuSize*h;
	}

	imageWin = window.open(sbUrl,randomName,'width='+w +',height='+h+',top=0,left=0,menu=no,scrollbars=0,statusbar=1');
	//tres horas>  1000 (sec) * 60 (min) * 60 (hour) * 4 
	//30 segundos= 1/2 Minuto.
	var maxtime=60000;

	if(nuSeconds==0) nuSeconds=maxtime;

	setTimeout("imageWin.close()", nuSeconds*1000 );  

}

function simad_openWindow ( sbUrl, nuSeconds )
{
  	// ** START **
	var r;
	var randomName;
	r=Math.floor(Math.random() * 100); 
	randomName='simadWindow'+r;
//	imageWin = window.open(sbUrl,randomName,'width=800,height=600,top=0,left=0,menu=no,scrollbars=1,statusbar=1,resizable=1');
	imageWin = window.open(sbUrl,27,'width=800,height=600,top=0,left=0,menu=no,scrollbars=1,statusbar=1,resizable=1');
	
  //tres horas>  1000 (sec) * 60 (min) * 60 (hour) * 4 
	//30 segundos= 1/2 Minuto.
	var maxtime=60000;

	if(nuSeconds==0) nuSeconds=maxtime;

	setTimeout("imageWin.close()", nuSeconds*1000 );  

}

function simad_closeWindow() {

	if(window.opener==null){
		window.close();
	}

	var sbLocation=window.opener.location.href.replace("#","");
    	//alert(window.opener.location.href);
	//alert(sbLocation);
	window.opener.location.href = sbLocation;
	if (window.opener.progressWindow)
	{
		window.opener.progressWindow.close()
	}
	window.close();
} 

function simad_refresh()
{
        var sbLocation=window.location.href.replace("#","");
    	//alert(window.opener.location.href);
	//alert(sbLocation);
	window.location.href = sbLocation;

}


function simad_goback()
{
        var sbLocation=history.back();

	sbLocation=sbLocation.replace("#","");

    	//alert(window.opener.location.href);
	//alert(sbLocation);
	window.location.href = sbLocation;

}
