    <!-- BEGIN information -->
    <div class="information">{information.INFORMATION}</div>
    <!-- END information -->
    <!-- BEGIN start_slideshow -->
    <div style="text-align:center;margin-bottom:5px;">
      [ {L_SLIDESHOW} :
      <!-- BEGIN second -->
      <a href="{start_slideshow.second.U_SLIDESHOW}" class="back" style="font-weight:bold;">{start_slideshow.second.SLIDESHOW_SPEED}</a>
      <!-- END second -->
      {L_TIME} ]
    </div>
    <!-- END start_slideshow -->
    <!-- BEGIN stop_slideshow -->
    <div style="text-align:center;margin-bottom:5px;">
      [ <a href="{stop_slideshow.U_SLIDESHOW}" class="back" style="font-weight:bold;">{L_STOP_SLIDESHOW}</a> ]
    </div>
    <!-- END stop_slideshow -->
    <table style="width:100%;height:100%;">
      <tr align="center" valign="middle">
        <td colspan="3" style="width:100%;"> 
		<div class="table2">
		<div class="titrePage">{CATEGORY}</div>
		<div class="titreImg">{PHOTO}</div>
          <a href="{U_HOME}">
            <img class="image" src="{SRC_IMG}" style="width:{WIDTH_IMG}px;height:{HEIGHT_IMG}px;" alt="{ALT_IMG}"/>
          </a>
		  <div class="nameImage">{TITLE}</div>
		  <!-- BEGIN legend -->
          <div class="commentImage">{COMMENT_IMG}</div>
		  <!-- END legend -->
		  </div>
          <table style="width:100%;">   
            <tr align="center" valign="middle">
			  <td style="width:30%;">
				<!-- BEGIN previous -->
				<a class="none" href="{U_PREV_IMG}" title="{L_PREV_IMG}{PREV_TITLE_IMG}">
				<img style="border:none;" width="30" height="100" src="template/default/theme/left-arrow.jpg" alt="" />
				  <img src="{PREV_IMG}" class="thumbLink" style="margin-right:10px;margin-left:5px;" alt="{PREV_TITLE_IMG}"/>
				  </a>
				<!-- END previous -->
			  </td>
			  <td style="width:40%;">
			  <table style="margin:auto;margin-top:5px;margin-bottom:5px;">
                  <!-- BEGIN info_line -->
                  <tr>
                     <td class="menu" style="font-weight:bold;">{info_line.INFO} : </td>
                     <td class="menu" style="text-align:right;">{info_line.VALUE}</td>
                  </tr>
                  <!-- END info_line -->
                </table>
			  </td>
			  <td style="width:30%;">
				<!-- BEGIN next -->
				  <a  class="none" href="{U_NEXT_IMG}" title="{L_NEXT_IMG}{NEXT_TITLE_IMG}">
				  <img class="thumbLink" src="{NEXT_IMG}" style="margin-right:10px;margin-left:5px;" alt="{NEXT_TITLE_IMG}"/>
				  <img style="border:none;" class="thumbLink" width="30" height="100" src="template/default/theme/right-arrow.jpg" alt="" />
				  </a>
				<!-- END next -->
			  </td>
			</tr>
          </table>
          <!-- BEGIN favorite -->
          <div class="menu" style="text-align:center;margin:5px;">
          <a href="{favorite.U_FAVORITE}" title="{favorite.FAVORITE_HINT}">
  			<img src="{favorite.FAVORITE_IMG}" style="border:none;margin-left:5px;" alt="" />{favorite.FAVORITE_ALT}
		  </a>
          </div>
          <!-- END favorite -->
          <!-- BEGIN modification -->
          <div class="menu" style="text-align:center;margin:5px;">
            [ <a href="{U_ADMIN}">{L_ADMIN}</a> ]
          </div>
          <!-- END modification -->
          <div style="text-align:center;">{L_BACK}</div>
        </td>
        <td>&nbsp;</td>
      </tr>
      <!-- BEGIN comments -->
      <tr align="center" valign="middle">
        <td colspan="3" class="table2">
                  <div class="commentTitle">
                    [{comments.NB_COMMENT}] {L_COMMENT_TITLE}
                  </div>
                  <div class="commentsNavigationBar">{comments.NAV_BAR}</div>
				  <table class="tablecompact">
                  <!-- BEGIN comment -->
				    <tr class="throw">
					  <td class="throw">
					  {comments.comment.COMMENT_AUTHOR}
					  </td>
					  <td colspan="2" class="commentDate">
					  {comments.comment.COMMENT_DATE}
					<!-- BEGIN delete -->
					  <a href="{comments.comment.delete.U_COMMENT_DELETE}" title="{L_DELETE_COMMENT}"><img src="{T_DEL_IMG}" style="border:none;vertical-align:middle; margin-left:5px;" alt="[{L_DELETE}]"/></a>
					<!-- END delete -->
					  </td>
					</tr>
					<tr class="row1">
					  <td class="comment" colspan="3">{comments.comment.COMMENT}</td>
					</tr>
                  <!-- END comment -->
            <!-- BEGIN add_comment -->
			<tr class="throw">
			  <td colspan="3">{L_ADD_COMMENT}</td>
			</tr>
			<form  method="post" action="{U_ADD_COMMENT}">
  		    <tr class="row1">
			  <td class="comment" >
                    <!-- BEGIN author_field -->
                    {L_AUTHOR}</td><td colspan="2"><input type="text" name="author" />
					</td></tr>
					<tr class="row1">
					<td class="comment" >
                    <!-- END author_field -->
                    <!-- BEGIN author_known -->
                    <input type="hidden" name="author"  value="{comments.add_comment.author_known.KNOWN_AUTHOR}" />
                    <!-- END author_known -->
                    {L_COMMENT}</td>
					<td style="width:100%;">
					<input name="content" type="text" maxlength="200" style="width:100%;" value="" /></td><td>
					<input type="submit" value="{L_SUBMIT}" class="bouton" />
			  </td>
			 </tr>
			 </form>
           	</table>
            <!-- END add_comment -->
        </td>
      </tr>
      <!-- END comments -->
    </table>
