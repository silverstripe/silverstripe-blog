<?php

/**
 * @deprecated since version 2.0
 *
 * @property int $ParentID
 * @property string $Date
 * @property string $PublishDate
 * @property string $Tags
 */
class BlogEntry extends BlogPost implements MigratableObject
{
    /**
     * @var string
     */
    private static $hide_ancestor = 'BlogEntry';

    /**
     * @var array
     */
    private static $db = array(
        'Date' => 'SS_Datetime',
        'Author' => 'Text',
        'Tags' => 'Text',
    );

    /**
     * {@inheritdoc}
     */
    public function canCreate($member = null, $context = array())
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {

        //Migrate comma separated tags into BlogTag objects.
        foreach ($this->TagNames() as $tag) {
            $existingTag = BlogTag::get()->filter(array('Title' => $tag, 'BlogID' => $this->ParentID));
            if ($existingTag->count()) {
                //if tag already exists we will simply add it to this post.
                $tagObject = $existingTag->First();
            } else {
                //if the tag is now we create it and add it to this post.
                $tagObject = new BlogTag();
                $tagObject->Title = $tag;
                $tagObject->BlogID = $this->ParentID;
                $tagObject->write();
            }

            if ($tagObject) {
                $this->Tags()->add($tagObject);
            }
        }

        //Store if the original entity was published or not (draft)
        $published = $this->IsPublished();
        // If a user has subclassed BlogEntry, it should not be turned into a BlogPost.
        if ($this->ClassName === 'BlogEntry') {
            $this->ClassName = 'BlogPost';
            $this->RecordClassName = 'BlogPost';
        }
        //Migrate these key data attributes
        $this->PublishDate = $this->Date;
        $this->AuthorNames = $this->Author;
        $this->InheritSideBar = true;

        //Write and additionally publish the item if it was published before.
        $this->write();
        if ($published) {
            $this->publish('Stage', 'Live');
            $message = "PUBLISHED: ";
        } else {
            $message = "DRAFT: ";
        }

        return $message . $this->Title;
    }

    /**
     * Safely split and parse all distinct tags assigned to this BlogEntry.
     *
     * @deprecated since version 2.0
     *
     * @return array
     */
    public function TagNames()
    {
        $tags = preg_split('/\s*,\s*/', trim($this->Tags));

        $results = array();

        foreach ($tags as $tag) {
            if ($tag) {
                $results[mb_strtolower($tag)] = $tag;
            }
        }

        return $results;
    }
}

/**
 * @deprecated since version 2.0
 */
class BlogEntry_Controller extends BlogPost_Controller
{
}
