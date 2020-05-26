$('#periodOfTime').change(function(){
    if (this.value == "customPeriod"){
		document.getElementById("periodOfTime").setAttribute("onclick","");
        $('#dateModal').modal({
            show: true
		
        });
    } else
	{
		document.getElementById("periodOfTime").setAttribute("onclick","this.form.submit()");
	}
});

