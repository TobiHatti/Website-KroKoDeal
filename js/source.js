function bgenScroll()
{
    // DESCRIPTION:
    // When using CSS-Targets, a <a href="#anchor"> is required.
    // To prevent the page from jumping to the anchor and therefor putting
    // it on top of the page, the <a>-Tag needs to contain the following event:
    // onclick="bgenScroll();"

    if (window.pageYOffset!= null)
    {
        st=window.pageYOffset+"";
    }

    if (document.body.scrollWidth!= null)
    {
        if (document.body.scrollTop)
        {
            st=document.body.scrollTop;
        }
        st=document.documentElement.scrollTop;
    }
    setTimeout("window.scroll(0,st)",10);
}

function ReadURL(input,outputImg)
{
    //input = this

    if (input.files && input.files[0])
    {
        var reader = new FileReader();
        reader.onload = function (e)
        {
            $('#' + outputImg)
                .attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}


function InsertCapUpdateCapPreview()
{
    var listpre = document.getElementById("capColor");
    var capColor = listpre.options[listpre.selectedIndex].value.split('-')[1];

    listpre = document.getElementById("baseColor");
    var baseColor = listpre.options[listpre.selectedIndex].value.split('-')[1];

    listpre = document.getElementById("textColor");
    var textColor = listpre.options[listpre.selectedIndex].value.split('-')[1];

    if(capColor=="") capColor = "GLD";
    if(baseColor=="") baseColor = "FFFFFF";
    if(textColor=="") textColor = "FF0000";

    document.getElementsByName("capPreviewCapColor")[0].id = capColor;
    document.getElementsByName("capPreviewBaseColor")[0].style.background = "#" + baseColor;
    document.getElementsByName("capPreviewTextColor")[0].style.color = "#" + textColor;

    if(document.getElementById("isTwistLock").checked) document.getElementsByName("capPreviewTwistLock")[0].id = "shown";
    else document.getElementsByName("capPreviewTwistLock")[0].id = "";

    if(document.getElementById("isUsed").checked) document.getElementsByName("capPreviewCapColor")[0].src = "/content/capNewColored.png";
    else document.getElementsByName("capPreviewCapColor")[0].src = "/content/capUsedColored.png";;
}









