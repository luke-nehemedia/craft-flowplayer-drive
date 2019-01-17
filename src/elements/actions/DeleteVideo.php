<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into Craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */
namespace lucasbares\craftflowplayerdrive\elements\actions;

use Craft;
use craft\base\ElementAction;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;
use craft\elements\db\ElementQueryInterface;
use yii\base\Exception;

/**
 * DeleteVideo represents a Delete Video element action.
 *
 * @author    Lucas Bares
 * @package   lucasbares\craftflowplayerdrive\elements\actions
 * @since     1.0.0
 */
class DeleteVideo extends ElementAction
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('app', 'Delete');
    }

    /**
     * @inheritdoc
     */
    public static function isDestructive(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('craft-flowplayer-drive', 'Are you sure you want to delete the selected videos?');
    }

    /**
     * Delete video via API, and then from the Database
     *
     * @param ElementQueryInterface $query
     * @return bool
     * @throws \Throwable
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        try {
            foreach ($query->all() as $video) {
                CraftFlowplayerDrive::getInstance()->flowplayerDriveService->deleteVideoElement($video);
                Craft::$app->getElements()->deleteElement($video);
            }
        } catch (Exception $exception) {
            $this->setMessage($exception->getMessage());

            return false;
        }

        $this->setMessage(Craft::t('craft-flowplayer-drive', 'Videos successfully deleted.'));

        return true;
    }
}