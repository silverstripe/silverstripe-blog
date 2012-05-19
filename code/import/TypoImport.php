<?php

require_once("model/DB.php");

class TypoImport extends Controller {
    /**
	 * Imports product status and price change updates.
	 * 
	 */

	function testinstall() {
		echo "Ok";	
	}
	
	/**
	 * Imports blog entries and comments from a Potgres-based typo installation into a SilverStripe blog
	 */
	function import(){
		// some of the guys in the contents table are articles, some are contents. Distinguished by type = "Article" or "Comment"
		// fields are: id, title, author, body, body_html, extended, excerpt, keywords, created_at, updated_at, extended_html, user_id, permalink, guid, [13]
		// 			   text_filter_id, whiteboard, type, article_id, email, url, ip, blog_name, name, published, allow_pings, allow_comments, blog_id
        //             published_at, state, status_confirmed

		
		$dbconn = pg_connect("host=orwell port=5432 dbname=typo_prod user=postgres password=possty");

		// create a new blogholder and call it "imported blog"
		$bholder = new BlogHolder(); 
		$bholder->Title = "imported blog";
		
		// write it!
		$bholder->write();
		$bholder->publish("Stage", "Live");
			
		// get the typo articles 
		$result = pg_query($dbconn, "SELECT * FROM contents WHERE type='Article'");
		
		while ($row = pg_fetch_row($result)) {
			
			// title [1]
			// author [2]
			// body [3]
			// body_html [4] (type rendered and cached the html here. This is the preferred blog entry content for migration) 
			// keywords (space separated) [7] (tags table is just a list of the unique variants of these keywords)
			// created_at [8]
			// permalink [12] (this is like the url in sitetree, prolly not needed)
			// email [18] (address of the commenter)
			// url [19] (url of the commenter)
			
			$title = $row[1];
			$author = $row[2];
			$blog_entry = $row[4];
			$keywords = $row[7];
			$created_at = $row[8];
			
			// sometimes it's empty. If it is, grab the body
			if ($blog_entry == ""){
				// use "body"
				$blog_entry = $row[3];
			}
			echo "blog_entry: $blog_entry";
  			echo "<br />\n";
  			
  			// put the typo blog entry in the SS database
			$newEntry = new BlogEntry();
			$newEntry->Title = $title;
			$newEntry->Author = $author;
			$newEntry->Content = $blog_entry;
			$newEntry->Tags = $keywords;
			$newEntry->Date = $created_at;
						
			// tie each blog entry back to the blogholder we created initially
			$newEntry->ParentID = $bholder->ID;
			
			// write it!
			$newEntry->write();
			$newEntry->publish("Stage", "Live");
  			
			// grab the id so we can get the comments
  			$old_article_id = $row[0];

  			// get the comments
			$result2 = pg_query($dbconn, "SELECT * FROM contents WHERE type = 'Comment' AND article_id = $old_article_id");

			while ($row2 = pg_fetch_row($result2)) {
				// grab the body_html
				$comment = $row2[4];
			
				// sometimes it's empty. If it is, grab the body
				if ($comment == ""){
					// use "body"
					$comment = $row2[3];
				}
				
				
				
				
				$Cauthor = $row2[2];
				$Ccreated_at = $row2[8];
			
	  			// 	put the typo blog comment in the SS database
				$newCEntry = new PageComment();
				$newCEntry->Name = $Cauthor;	
				$newCEntry->Comment = $comment;
				$newCEntry->Created = $created_at;
						
				// need to grab the newly inserted blog entry's id
				$newCEntry->ParentID = $newEntry->ID;
							
				// write it!
				$newCEntry->write();

				echo "comment: $comment";
	  			echo "<br />\n";
			}
		
			$newEntry->flushCache();
			
			// fix up the specialchars
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#215;\", \"x\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8217;\", \"&rsquo;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8216;\", \"&lsquo;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#151;\", \"&mdash;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8220;\", \"&ldquo;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8221;\", \"&rdquo;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8211;\", \"&ndash;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8212;\", \"&mdash;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8230;\", \"&hellip;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#8482;\", \"&trade;\")");
			pg_query($dbconn, "UPDATE SiteTree SET Content = REPLACE(Content, \"&#38;\", \"&amp;\")");
			
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#215;\", \"x\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8217;\", \"&rsquo;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8216;\", \"&lsquo;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#151;\", \"&mdash;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8220;\", \"&ldquo;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8221;\", \"&rdquo;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8211;\", \"&ndash;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8212;\", \"&mdash;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8230;\", \"&hellip;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#8482;\", \"&trade;\")");
			pg_query($dbconn, "UPDATE PageComment SET Comment = REPLACE(Comment, \"&#38;\", \"&amp;\")");
			
			
		}
		
		pg_close($dbconn);
		
	} // end function

} // end class
?>
