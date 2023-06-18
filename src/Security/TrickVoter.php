<?php 
namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TrickVoter extends Voter
{
    // these strings are just invented: you can use anything+

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    
    public function __construct(private Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on `Trick` objects
        if (!$subject instanceof Trick) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Trick object, thanks to `supports()`
        /** @var Trick $trick */
        $trick = $subject;

        return match($attribute) {
            // self::VIEW => $this->canView($trick, $user),
            self::EDIT => $this->canEdit($trick, $user),
            self::DELETE => $this->canDelete($trick, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    // private function canView(Trick $trick, User $user): bool
    // {
    //     // if they can edit, they can view
    //     if ($this->canEdit($trick, $user)) {
    //         return true;
    //     }

    //     // the Trick object could have, for example, a method `isPrivate()`
    //     return !$trick->isPrivate();
    // }

    private function canEdit(Trick $trick, User $user): bool
    {
        // this assumes that the Trick object has a `getOwner()` method
        return $user === $trick->getUser();
    }

    private function canDelete(Trick $trick, User $user): bool
    {
        // this assumes that the Trick object has a `getOwner()` method
        return $user === $trick->getUser();
    }
}