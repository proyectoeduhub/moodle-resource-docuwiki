Moodle DocuWiki Resource
Easily embeds Docuwiki pages
for Moodle 1.9


Needs resource/lib.php modification. (I love Moodle OO)




function resource_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php
///

	global $CFG;

	$info = NULL;

	if ($resource = get_record("resource", "id", $coursemodule->instance)) {
		if (!empty($resource->popup)) {
			$info->extra =  urlencode("onclick=\"this.target='resource$resource->id'; return ".
									"openpopup('/mod/resource/view.php?inpopup=true&amp;id=".
									$coursemodule->id.
									"','resource$resource->id','$resource->popup');\"");
		}

		require_once($CFG->libdir.'/filelib.php');
		//echo $resource->type;
		if ($resource->type == 'file') {
			$icon = mimeinfo("icon", $resource->reference);
			if ($icon != 'unknown.gif') {
					$info->icon ="f/$icon";
			} else {
					$info->icon ="f/web.gif";
			}
		} else if ($resource->type == 'directory') {
			$info->icon ="f/folder.gif";
		} else if ($resource->type == 'docuwiki') {
			$info->icon ="";

			$domain = getdomain($resource->alltext);
			if ($domain != "ejemplo.com") {
				$probando = probando($resource->alltext);
				$probando = str_replace("/wiki/", $domain."/wiki/", $probando);
			}
		}
	}

	return $info;
} 
