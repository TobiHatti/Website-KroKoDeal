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