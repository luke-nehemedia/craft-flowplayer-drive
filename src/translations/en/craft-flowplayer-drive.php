<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

/**
 * Craft Flowplayer Drive en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('craft-flowplayer-drive', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    Lucas Bares
 * @package   CraftFlowplayerDrive
 * @since     1.0.0
 */
return [
    'Craft Flowplayer Drive plugin loaded' => 'Craft Flowplayer Drive plugin loaded',

    // Settings
    'Site-ID' => 'Site-ID',
    'Site-ID-instructions' => 'You can find your site-id under your <a href="https://flowplayer.com/app/workspace/settings" target="_blank">workspace settings</a> in flowplayer drive.',
    'API-Key' => 'API-Key',
    'API-Key-instructions' => 'You can find your API-key under your <a href="https://flowplayer.com/app/workspace/settings" target="_blank">workspace settings</a> in flowplayer drive.',
    'User-ID' => 'User-ID',
    'User-ID-instructions' => 'You can find your User-id under your <a href="https://flowplayer.com/app/user/account" target="_blank">user account settings</a> in flowplayer drive, right under your profile picture. The user-id is necessary to create new videos and to delete videos.',
    'Default Player-ID' => 'Default Player-ID',
    'Default Player-ID-instructions' => 'If you want to use the provided embed code, you have to insert a default player-id here. You can find a <a href="https://flowplayer.com/app/players/" target="_blank">list of your players</a> at flowplayer drive. You can extract the player-ID either from the URL or from the embed-code.',


    // CP Labels
    'Flowplayer Drive' => 'Flowplayer Drive',
    'Video List'  => 'List videos',
    'New Video'   => 'New Video',
    'Create a new video' => 'Create a new Video',
    'Edit video' => 'Edit Video',
    'Refresh Videolist' => 'Refresh Videolist',
    'Clear Videolist' => 'Clear Videolist',
    'Refresh Index' => 'Refresh Index',

    // Field
    'FlowplayerVideoField' => 'Flowplayer Drive Video',
    'Choose video'  => 'Choose video',
    'Display only published videos' => 'Display only published videos',
    'Private or unpublished videos will not be selectable' => 'Private or unpublished videos will not be selectable',
    'Limit' => 'Limit',
    'Limit the number of selectable videos' => 'Limit the number of selectable videos',

    // Sources
    'All Videos'    => 'All Videos',
    'Public Videos'    => 'Public Videos',
    'Private Videos'    => 'Private Videos',

    // Statuses
    'Published State'   =>  'Published',
    'Private State'     =>  'Private',

    // Attributes
    'Name'          =>  'Name',
    'Created at'    =>  'Created at',
    'Published at'  =>  'Published at',
    'Updated at'    =>  'Updated at',
    'Views'         =>  'Views',
    'Published'     =>  'Published',
    'Video-ID'      =>  'Video-ID',
    'Entry-ID'      =>  'Entry-ID',
    'Status'        =>  'Status',
    'Description'   =>  'Description',


    // Messages
    'Are you sure you want to delete the selected videos?'   =>  'Are you sure you want to delete the selected videos?',
    'Videos successfully deleted.' => 'Videos successfully deleted.',
    'Video successfully saved.' => 'Video successfully saved.',

    'Videolist refreshed. {updated} videos updated, {new} new videos indexed.' => 'Videolist refreshed. {updated} videos updated, {new} new videos indexed.',
    'Videolist cleared' => 'Videolist cleared',

    // Instructions


];
