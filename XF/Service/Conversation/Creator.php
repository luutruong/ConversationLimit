<?php

namespace Truonglv\ConversationLimit\XF\Service\Conversation;

use XF\Entity\User;

class Creator extends XFCP_Creator
{
    protected function _validate()
    {
        $validated = parent::_validate();

        $limit = (int) $this->starter->hasPermission('conversation', 'tcl_maxConvos');
        if ($limit !== -1) {
            $totalCreated = $this->finder('XF:ConversationMaster')
                ->where('user_id', $this->starter->user_id)
                ->total();

            if ($totalCreated >= $limit) {
                $recipients = $this->recipients;
                /** @var User $recipient */
                foreach ($recipients as $recipient) {
                    if ($recipient->is_admin || $recipient->is_moderator) {
                        return $validated;
                    }
                }

                $validated[] = \XF::phrase('tcl_you_have_reached_maximum_conversations_can_create_allow_x', [
                    'limit' => $limit
                ]);
            }
        }

        return $validated;
    }
}
