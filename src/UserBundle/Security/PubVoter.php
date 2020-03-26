<?php

namespace UserBundle\Security;

use UserBundle\Entity\User;
use UserBundle\Entity\Pub;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
/**
 * 
 */
class PubVoter extends Voter
{
	const VIEW='view';
	const EDIT='edit';

	 private $decisionManager;

	 public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
        //throw new Exception("UID : ".$user->getUserid(). " PUB UID : "$pub->getUser()->getUserid(), 1);
    }

	protected function supports($attribute, $subject)
	{

        // if the attribute isn't one we support, return false
		if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
			return false;
		}

        // only vote on Post objects inside this voter
		if (!$subject instanceof Pub) {
			return false;
		}

		return true;
	}

	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		$user = $token->getUser();

		if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
			return false;
		}

		// ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        // you know $subject is a Post object, thanks to supports
		/** @var Pub $pub */
		$pub = $subject;

		switch ($attribute) {
			case self::VIEW:
				return $this->canView($pub, $user);
			case self::EDIT:
				return $this->canEdit($pub, $user);
		}

		

		throw new \LogicException('This code should not be reached!');
	}

	private function canView(Pub $pub, User $user)
	{
        // if they can edit, they can view
		if ($this->canEdit($pub, $user)) {
			return true;
		}

        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property
		//return !$pub->isPrivate();
		return true; //Every body can see published
	}

	private function canEdit(Pub $pub, User $user)
	{
        // this assumes that the data object has a getOwner() method to get the entity of the user who owns this data object

		return $user === $pub->getUser();
	}


}