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
                $uuid = $this->uuidModel->getUuid($user->mcname);
                $this->userRepository->setUUID($user->id, $uuid);
            }
        } catch (RuntimeException $exc) {
            //echo nothing critical
        }
    }

}
