<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecuredPresenter {

    /** @var \DB\UserRepository @inject */
    public $userRepository;

    /** @var \UUIDModel @inject */
    public $uuidModel;

    public function beforeRender() {
        parent::beforeRender();
        $nullUsers = $this->userRepository->findBy(array('uuid' => NULL, 'NOT mcname' => NULL));
        try {
            foreach ($nullUsers as $user) {
                $identity = $this->uuidModel->getUuid($user->mcname, TRUE);
                $this->userRepository->setUUID($user->id, $identity['uuid']);
                $this->userRepository->setMcNick($user->id, $identity['name']);
            }
        } catch (RuntimeException $exc) {
            //echo nothing critical
        }
    }

}
