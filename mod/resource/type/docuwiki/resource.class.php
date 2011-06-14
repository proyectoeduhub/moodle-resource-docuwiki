<?php // $Id: resource.class.php,v 1.34.2.3 2007/07/06 08:38:42 urs_hunkler Exp $

class resource_docuwiki extends resource_base {


function resource_docuwiki($cmid=0) {
    parent::resource_base($cmid);
}

function add_instance($resource) {
    $this->_postprocess($resource);
    return parent::add_instance($resource);
}


function update_instance($resource) {
    $this->_postprocess($resource);
    return parent::update_instance($resource);
}


function resource_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php
///

   global $CFG;

   $info = NULL;
/*
   if ($resource = get_record("resource", "id", $coursemodule->instance)) {
       if (!empty($resource->popup)) {
           $info->extra =  urlencode("onclick=\"this.target='resource$resource->id'; return ".
                            "openpopup('/mod/resource/view.php?inpopup=true&amp;id=".
                            $coursemodule->id.
                            "','resource$resource->id','$resource->popup');\"");
       }

       require_once($CFG->libdir.'/filelib.php');

       if ($resource->type == 'file') {
           $icon = mimeinfo("icon", $resource->reference);
           if ($icon != 'unknown.gif') {
               $info->icon ="f/$icon";
           } else {
               $info->icon ="f/web.gif";
           }
       } else if ($resource->type == 'directory') {
           $info->icon ="f/folder.gif";
       }
   }
*/
$content = "holaaaaaaaaaaaaaaj";
$info->extra = urlencode($content);

   return $info;
}



function _postprocess(&$resource) {
    global $RESOURCE_WINDOW_OPTIONS;
    $alloptions = $RESOURCE_WINDOW_OPTIONS;

    if ($resource->windowpopup) {
        $optionlist = array();
        foreach ($alloptions as $option) {
            $optionlist[] = $option."=".$resource->$option;
            unset($resource->$option);
        }
        $resource->popup = implode(',', $optionlist);
        unset($resource->windowpopup);
        $resource->options = '';

    } else {
        if (empty($resource->blockdisplay)) {
            $resource->options = '';
        } else {
            $resource->options = 'showblocks';
        }
        unset($resource->blockdisplay);
        $resource->popup = '';
    }
}


function display() {
    global $CFG;

    $formatoptions = new object();
    $formatoptions->noclean = true;

    /// Are we displaying the course blocks?
    if ($this->resource->options == 'showblocks') {

        parent::display_course_blocks_start();

        $content = format_text($this->resource->alltext, FORMAT_HTML, $formatoptions, $this->course->id);
$content = "holaa";
echo $content;
        parent::display_course_blocks_end();

    } else {

        /// Set up generic stuff first, including checking for access
        parent::display();

        /// Set up some shorthand variables
        $cm = $this->cm;
        $course = $this->course;
        $resource = $this->resource;

        $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));
        $inpopup = optional_param('inpopup', '', PARAM_BOOL);
        
        // fix for MDL-9021, thanks Etienne Roz�
        add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);

        if ($resource->popup) {
            if ($inpopup) {                    /// Popup only
                
                print_header();
                print_simple_box(format_text($resource->alltext, FORMAT_HTML, $formatoptions, $course->id),
                        "center clearfix", "", "", "20");
                print_footer($course);
            } else {                           /// Make a page and a pop-up window

                print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name),
                        "", "", true, update_module_button($cm->id, $course->id, $this->strresource),
                        navmenu($course, $cm));

                echo "\n<script type=\"text/javascript\">";
                echo "\n//<![CDATA[\n";
                echo "openpopup('/mod/resource/view.php?inpopup=true&id={$cm->id}','resource{$resource->id}','{$resource->popup}');\n";
                echo "\n//]]>\n";
                echo '</script>';

                if (trim(strip_tags($resource->summary))) {
                    print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center clearfix");
                }

                $link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&amp;id={$cm->id}\" onclick=\"this.target='resource{$resource->id}'; return openpopup('/mod/resource/view.php?inpopup=true&amp;id={$cm->id}', 'resource{$resource->id}','{$resource->popup}');\">".format_string($resource->name,true)."</a>";

                echo '<div class="popupnotice">';
                print_string('popupresource', 'resource');
                echo '<br />';
                print_string('popupresourcelink', 'resource', $link);
                echo '</div>';

                print_footer($course);
            }
        } else {    /// not a popup at all

            print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name),
                    "", "", true, update_module_button($cm->id, $course->id, $this->strresource),
                    navmenu($course, $cm));

            print_simple_box(format_text($resource->alltext, FORMAT_HTML, $formatoptions, $course->id), "center clearfix", "", "", "20");

            $strlastmodified = get_string("lastmodified");
            echo "<div class=\"modified\">$strlastmodified: ".userdate($resource->timemodified)."</div>";

            print_footer($course);
        }

    }

}

function setup_preprocessing(&$defaults){

    if (!isset($defaults['popup'])) {
        // use form defaults

    } else if (!empty($defaults['popup'])) {
        $defaults['windowpopup'] = 1;
        if (array_key_exists('popup', $defaults)) {
            $rawoptions = explode(',', $defaults['popup']);
            foreach ($rawoptions as $rawoption) {
                $option = explode('=', trim($rawoption));
                $defaults[$option[0]] = $option[1];
            }
        }
    } else {
        $defaults['windowpopup'] = 0;
        if (array_key_exists('options', $defaults)) {
            $defaults['blockdisplay'] = ($defaults['options']=='showblocks');
        }
    }
}

function setup_elements(&$mform) {
    global $CFG, $RESOURCE_WINDOW_OPTIONS;

   // $mform->addElement('htmleditor', 'alltext', get_string('fulltext', 'resource'), array('cols'=>85, 'rows'=>30));
    $mform->addElement('text', 'alltext', "URL", array('size'=>100));
    $mform->setType('alltext', PARAM_RAW);
    $mform->setHelpButton('alltext', array('reading', 'writing', 'richtext'), false, 'editorhelpbutton');
    $mform->addRule('alltext', get_string('required'), 'required', null, 'client');

    $mform->addElement('header', 'displaysettings', get_string('display', 'resource'));

    $woptions = array(0 => get_string('pagewindow', 'resource'), 1 => get_string('newwindow', 'resource'));
    $mform->addElement('select', 'windowpopup', get_string('display', 'resource'), $woptions);
    $mform->setDefault('windowpopup', (int)!empty($CFG->resource_popup));

    $mform->addElement('checkbox', 'blockdisplay', get_string('showcourseblocks', 'resource'));
    $mform->setDefault('blockdisplay', 0);
    $mform->disabledIf('blockdisplay', 'windowpopup', 'eq', '1');
    $mform->setAdvanced('blockdisplay');

    foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
        if ($option == 'height' or $option == 'width') {
            $mform->addElement('text', $option, get_string('new'.$option, 'resource'), array('size'=>'4'));
            $mform->setDefault($option, $CFG->{'resource_popup'.$option});
            $mform->disabledIf($option, 'windowpopup', 'eq', '0');
        } else {
            $mform->addElement('checkbox', $option, get_string('new'.$option, 'resource'));
            $mform->setDefault($option, $CFG->{'resource_popup'.$option});
            $mform->disabledIf($option, 'windowpopup', 'eq', '0');
        }
        $mform->setAdvanced($option);
    }
}


}

?>
