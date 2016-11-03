/*
var memberlist = [
    "Member 1",
    "Member 2",
    "Member 3",
    "Member 4",
    "CPU 1",
    "CPU 2",

];
*/

var memberlist = [
    "安保　建朗",
    "Ghita Athalina",
    "倉嶋　俊",
    "小林　優稀",
    "室井　健一",
    "森田　和貴",
    "渡辺　宇",
    "荒木　香名",
    "柴沢　弘樹",
];


var existMember = [];
var roulette;

var autoStopTime = 3; //sec

/***********************
Define Roulette Class
***********************/
function Roulette(ele, idx) {
    this.element = ele;
    this.memlist = existMember;
    this._idx = idx;
    this._y = 0;
    this.randomIdx = null;
};

Roulette.prototype = {
    ymax : 18,//calc from css
    dy : -1, //px
    dt : 1, //msec
    slowDy : -1, //px
    slowDt : 20, //msec
    stopidx : -1,
    timehdl : null,
    
    getShiftIdx : function(n) {
	var max = this.memlist.length;
	var shiftIdx = (n + this.idx) % max;
	if( shiftIdx < 0 ) { shiftIdx = shiftIdx + max; }
	return shiftIdx;
    },

    setInnerHTML : function() {
        this.element.innerHTML =
	    "<p>" + this.memlist[this.idx] + "</p>" +
	    "<p>" + this.memlist[this.getShiftIdx(1)] + "</p>";
    },

//     postJump : function(_url, _keys, _vals){
//     var html = '<form method="post" action="'+_url+'" id="postjump" style="display: none;">';
//     for(var cnt=0;cnt<_keys.length;cnt++){
//         html += '<input type="hidden" name="'+_keys[cnt]+'" value="'+_vals[cnt]+'" >';
//     }
//     html += '</form>';
//     $("body").append(html);
//     $('#postjump').submit();
//     $('#postjump').remove();
// 　　},

    stepMove : function () {
	this.y += this.dy;
	for( var i = 0; i < this.element.childNodes.length; i++ ) {
	    this.element.childNodes[i].style.transform
		= "translateY(" + this.y + "px)";
	}
    },
    startRoll : function () {
	this.thdl = setInterval(this.stepMove.bind(this), this.dt);
    },

    stopRoll : function() {
	clearInterval(this.thdl);
	this.dy = this.slowDy;
	this.dt = this.slowDt;

	this.stoploop();
    },
    stoploop : function() {
	if ( this.idx == this.stopIdx ) {
	    if ( this.y == 0 ) {
		this.element.innerHTML =
		    "<p>" + this.memlist[this.stopIdx] + "</p>";
		return;
	    }
	}
	this.stepMove();
	setTimeout( this.stoploop.bind(this), this.dt );
    },

    // postJump : function(){
    // var _url = 'index.php' ;
    // var _keys = randomIdx ;
    // var _vals = randomIdx ;

    // var html = '<form method="post" action="'+_url+'" id="postjump" style="display: none;">';
    // for(var cnt=0;cnt<_keys.length;cnt++){
    //     html += '<input type="hidden" name="'+_keys[cnt]+'" value="'+_vals[cnt]+'" >';
    // }
    // html += '</form>';
    // $("body").append(html);
    // $('#postjump').submit();
    // $('#postjump').remove();
    // },
};

Object.defineProperties(Roulette.prototype, {
    y: {
	get : function() { return this._y; },
	set : function(n) {
	    if ( this.ymax <= Math.abs(n) ) { this.idx++; }
	    this._y = n % this.ymax;
	},
    },
    idx: {
	get : function() { return this._idx; },
	set : function(n) {
	    var max = this.memlist.length;
	    this._idx = n % max;
	    if( this._idx < 0 ) { this._idx = n + max; }
	    this.setInnerHTML();
	},
    },
});


/************************
function
************************/
function printNamelist() {
    var ele = document.getElementById("namelist");
    for ( i = 0; i < memberlist.length; i++ ) {	
	var apndObj = document.createElement("p");
	apndObj.innerHTML =
	    "<label>" +
	    "<input type=\"checkbox\" name=\"exist\" checked onChange=\"updateMember()\">" +
	    memberlist[i] +
	    "</label>";
	ele.appendChild(apndObj);
    }
}

function getExistMember() {
    existMember = [];
    var ele = document.nameform.exist;
    for ( var i = 0; i < ele.length; i++ ) {
	if (ele[i].checked) {
	    existMember.push(memberlist[i]);
	}
    }
}

function printTable() {
    var ele = document.getElementById("resultTable");
    var strHtml = "";
    for ( var i = 0; i < existMember.length; i++ ) {
	strHtml +=
	    "<tr>" +
	    "<th>" + (i+1) + "</th>" +
	      "<td>" +
	        "<div class=\"frame\">" +
	          "<p>" + existMember[i] + "</p>" +
	        "</div>" +
	      "</td>" +
	    "</tr>";
    }
    ele.innerHTML = strHtml;

    // for ( var i = 0; i < existMember.length; i++ ) {
    //     person += existMember[i];
    // }

    //document.write(ele.innerHTML);
}


// function sampleForm( value ){
//     var form = document.createElement( ‘form’ );
//     document.body.appendChild( form );
//     var input = document.createElement( ‘input’ );
//     input.setAttribute( ‘type’ , ‘hidden’ );
//     input.setAttribute( ‘name’ , ‘name’ );
//     input.setAttribute( ‘value’ , value );
//     form.appendChild( input );
//     form.setAttribute( ‘action’ , ‘/index.php’ );
//     form.setAttribute( ‘method’ , ‘post’ );
//     form.submit();

// }

function updateMember() {
    getExistMember();
    printTable();
    //postJump(./index.php, strHtml2,  );
    //sampleForm();
}


function start() {
    var ele = document.getElementsByTagName("div");
    var elelen = ele.length;

    if ( elelen == 0 ) {
	alert("ERROR: Nobody are selected.\n");
	return;
    }

    for ( var i = 0; i < document.nameform.elements.length; i++ ) {
	document.nameform.elements[i].disabled = true;
    }

    var randomIdx = new Array(elelen);
    for ( var i = 0; i < elelen; i++ ) { randomIdx[i] = i; }
    randSort(randomIdx);
	
    roulette = new Array(elelen);

    



    for ( var i = 0; i < elelen; i++ ) {
	roulette[i] = new Roulette(ele[i], i);
	roulette[i].setInnerHTML();
	roulette[i].stopIdx = randomIdx[i];
	roulette[i].startRoll();
    }
    
    //postJump('index.php', randomIdx, randomIdx);


    //todo: set ymax

    if ( 0 < autoStopTime ) { setTimeout(stop, autoStopTime*1000); }
}


function stop() {
    for ( var i = 0; i < roulette.length; i++ ) {
	roulette[i].stopRoll();
    }
    for ( var i = 0; i < document.nameform.elements.length; i++ ) {
	document.nameform.elements[i].disabled = false;
    }
}

function randSort(a) {
    var len = a.length;
    /*
      random()*lenでは、乱数の偏りが無視できないと考えられる。
      明らかに1人目の順番が早くなる傾向にあった。
      またChromeでは、このようにしても偏りが見られた。
    */
    /*
    for ( var i = 0; i < len; i++ ) {
	var randIdx = Math.floor(Math.random()*len*len%len);
	var buf = a[randIdx];
	a[randIdx] = a[i];
	a[i] = buf;
    }
    */

    /*
      アルゴリズムには検討の余地がある。
      しかし現状、順番の偏りが少ないと考えられるため採用した。
     */
    for ( var i = 0; i < len*10; i++ ) {
	var idx1 = Math.floor(Math.random()*len);
	var idx2 = Math.floor(Math.random()*len);
	var buf = a[idx1];
	a[idx1] = a[idx2];
	a[idx2] = buf;
    }

}

function postJump(){
    var _url = 'index.php' ;
    var _keys = this.randomIdx ;
    var _vals = this.randomIdx ;

    var html = '<form method="post" action="'+_url+'" id="postjump" style="display: none;">';
    for(var cnt=0;cnt<_keys.length;cnt++){
        html += '<input type="hidden" name="'+_keys[cnt]+'" value="'+_vals[cnt]+'" >';
    }
    html += '</form>';
    $("body").append(html);
    $('#postjump').submit();
    $('#postjump').remove();
    
}


function randTest(n, m) {
    var a = new Array(n);
    var r = new Array(n);
    for ( var j = 0; j < n; j++ ) { r[j] = 0; }
    for ( var i = 0; i < m; i++ ) {
	for ( var j = 0; j < n; j++ ) { a[j] = j; }
	randSort(a);
	for ( var j = 0; j < n; j++ ) { r[j] += a[j]; }
    }
    console.log(r);
}

/***********************
execution
************************/
window.onload = function(){printNamelist(); updateMember();};
