//Actions
$(function(){

		//Default
		$("#loading").css("display","none");
		var base_url = $("#base_url").val();
	
		//home search
		$("#home_news_search").click(function(){
			var err = "";
			if($("#keyword").val() == ""){
				err += "Keyword harus diisi <br/>";
			}
			if($("#ref1").val() == ""){
				err += "Kategori harus dipilih <br/>";
			}
			if(err == ""){
				$("#home_news_form").submit();
			}else{
				alert(err);
			}
			return false;
		});
		
		//home search
		$("#disease_search").click(function(){
			var err = "";
			if($("#keyword").val() == ""){
				err += "Keyword harus diisi <br/>";
			}
			
			if(err == ""){
				$("#disease_search_form").submit();
			}else{
				alert(err);
			}
			return false;
		});
		 		 
});

function next(url)
{
	window.location = url+"game/list_video/";
}

