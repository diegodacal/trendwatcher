/*function wordFrequency(txt){
	var wordArray = txt.split(/[.?!,*'"]/);
	var newArray = [];
    var words = {};
	$.each(wordArray, function (ix, word) {
		if (newArray.length >= 1){
			newArray.some(function (w){
				if (w.text === word){
					w.size++;
				} else {
					newArray.push({text: word, size: 1});
				}
			});
		} else {
			newArray.push({text: word, size: 1000});
		}
	});
	return newArray;
}*/
var ignoreWords = ["","a","e","o","da","de","do","na","no","as","os","das","dos","nas","nos","em","la","el","lo","x","on","por","que","te","me","se","mas","como","http","to","t","co"];
function wordFrequency(txt){
    var wordArray = txt.split(/[ .?!,*'"/:]/);
    var newArray = [], wordObj;
    $.each(wordArray, function (ix, word) {
		if ($.inArray(word,ignoreWords)<0){
			wordObj = newArray.filter(function (w){
			   return w.text == word;
			});
			if (wordObj.length) {
				wordObj[0].size += 1;
			} else {
				newArray.push({text: word, size: 1});
			}
		}
	});
	newArray.sort(function(a,b){return a.size<b.size});
    return newArray;
}

$(function () {
	$(".trendLink").click(function(){
		$("#trendInput").attr("value", $(this).text());
		$("#viewTrend").submit();
		
	});
});

function texttoelement(text, elem){
	$(document).ready(function(){
		$(elem).append("<span>"+text+"</span>")
	});
}

function generatewordcloud(){	
	/* Create wordcloud */
	var twitterContent = "<?php  if(exist($twitterContent)){echo strip_tags(strtolower($twitterContent));}  ?>";
	var wordMap = {};
	wordMap = wordFrequency(twitterContent);
	console.log(JSON.stringify(wordMap));

	var fill = d3.scale.category20();

	d3.layout.cloud()
		.size([1000,300])
		.words(wordMap)
		.padding(3)
		.rotate(function() { return 0; })
		.font("verdana")
		.fontSize(function(d) { return d.size*3; })
		.on("end", draw)
		.start();

		function draw(words) {
	d3.select("#wordcloud").append("svg")
		.attr("width", 1000)
		.attr("height", 300)
		.append("g")
		.attr("transform", "translate(500,150)")
		.selectAll("text")
		.data(words)
		.enter().append("text")
		.style("font-size", function(d) { return d.size + "px"; })
		.style("font-family", "verdana")
		.style("fill", function(d, i) { return fill(i); })
		.attr("text-anchor", "middle")
		.attr("transform", function(d) {
			return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
		})
		.text(function(d) { return d.text; });
	}

} 