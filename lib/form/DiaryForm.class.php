<?php

/**
 * Diary form.
 *
 * @package    OpenPNE
 * @subpackage form
 * @author     Rimpei Ogawa <ogawa@tejimaya.com>
 */
class DiaryForm extends BaseDiaryForm
{
  public function configure()
  {
    unset($this['member_id']);
    unset($this['created_at']);
    unset($this['updated_at']);

    $this->widgetSchema['title'] = new sfWidgetFormInput();

    $this->widgetSchema['public_flag'] = new sfWidgetFormChoice(array(
      'choices'  => DiaryPeer::getPublicFlags(),
      'expanded' => true,
    ));
    $this->validatorSchema['public_flag'] = new sfValidatorChoice(array(
      'choices' => array_keys(DiaryPeer::getPublicFlags()),
    ));

    $this->mergeForm(new DiaryImageForm());
  }

  public function save($con = null)
  {
    $diary = parent::save();

    $imageKeys = array('photo_1', 'photo_2', 'photo_3');
    foreach ($imageKeys as $imageKey)
    {
      if ($this->getValue($imageKey))
      {
        $file = new File();
        $file->setFromValidatedFile($this->getValue($imageKey));
        $file->setName('d_'.$diary->getId().'_'.$file->getName());

        $image = new DiaryImage();
        $image->setDiary($diary);
        $image->setFile($file);
        $image->save();
      }
    }

    return $diary;
  }
}
