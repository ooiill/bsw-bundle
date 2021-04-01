<?php

namespace Leon\BswBundle\Entity;

use Leon\BswBundle\Entity\FoundationEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Leon\BswBundle\Annotation\Entity as BswAnnotation;
use Leon\BswBundle\Module\Entity\Abs as BswAbs;
use Leon\BswBundle\Module\Entity\Enum as BswEnum;
use Leon\BswBundle\Module\Hook\Entity as BswHook;
use Leon\BswBundle\Module\Form\Entity as BswForm;
use Leon\BswBundle\Module\Filter\Entity as BswFilter;
use Leon\BswBundle\Component\Helper as BswHelper;

/**
 * @ORM\Entity(repositoryClass="Leon\BswBundle\Repository\BswAdminMenuRepository")
 */
class BswAdminMenu extends FoundationEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="`id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=1, align="center", width=90, render=BswAbs::RENDER_CODE)
     * @BswAnnotation\Persistence(sort=1, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=1, type=BswForm\Number::class)
     * @BswAnnotation\Mixed(order=true)
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="`menu_id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=2, align="center", enumExtra=true, width=200)
     * @BswAnnotation\Persistence(sort=2, type=BswForm\Select::class, enumExtra=true, disabledOverall=false, typeArgs={"previewRoute": "app_bsw_admin_menu_preview", "previewArgs": {"filter": {"menu_id@0": 0}}})
     * @BswAnnotation\Filter(sort=2, type=BswForm\Select::class, enumExtra=true)
     */
    protected $menuId = 0;

    /**
     * @ORM\Column(type="string", name="`route_name`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=64, groups={"modify"})
     * @BswAnnotation\Preview(sort=3, enumExtra=true)
     * @BswAnnotation\Persistence(sort=3, type=BswForm\Select::class, enumExtra=true)
     * @BswAnnotation\Filter(sort=3, type=BswForm\Select::class, enumExtra=true)
     * @BswAnnotation\Mixed(order=true)
     */
    protected $routeName = "";

    /**
     * @ORM\Column(type="string", name="`icon`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=64, groups={"modify"})
     * @BswAnnotation\Preview(sort=4, render=BswAbs::RENDER_ICON, width=240)
     * @BswAnnotation\Persistence(sort=4)
     * @BswAnnotation\Filter(sort=4)
     */
    protected $icon = "";

    /**
     * @ORM\Column(type="string", name="`value`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=32, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=5, render=BswAbs::HTML_TEXT, width=200, align="center", hook={0:BswHook\TwigTrans::class})
     * @BswAnnotation\Persistence(sort=5)
     * @BswAnnotation\Filter(sort=5)
     */
    protected $value;

    /**
     * @ORM\Column(type="string", name="`javascript`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=256, groups={"modify"})
     * @BswAnnotation\Preview(sort=6, render=BswAbs::HTML_TEXT)
     * @BswAnnotation\Persistence(sort=6, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=6)
     */
    protected $javascript = "";

    /**
     * @ORM\Column(type="string", name="`json_params`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=7, width=360, hook={0:BswHook\JsonStringify::class}, render=BswAbs::HTML_JSON)
     * @BswAnnotation\Persistence(sort=7, hook={0:BswHook\JsonStringify::class}, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=7)
     */
    protected $jsonParams;

    /**
     * @ORM\Column(type="integer", name="`sort`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=5.01, align="center", render=BswAbs::RENDER_CODE)
     * @BswAnnotation\Persistence(sort=8, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=8, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=8.01, type=BswForm\Select::class, placeholder="Mode", group="sort", style={"width": "35%"}, enum=BswFilter\Senior::MODE_FULL, column=4)
     * @BswAnnotation\Filter(sort=8.02, type=BswForm\Input::class, placeholder="Value", group="sort", style={"width": "65%"}, title="Split by comma")
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $sort = 0;

    /**
     * @ORM\Column(type="string", name="`remark`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=128, groups={"modify"})
     * @BswAnnotation\Preview(sort=9, render=BswAbs::HTML_PRE, width=360)
     * @BswAnnotation\Persistence(sort=9, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=9)
     */
    protected $remark = "";

    /**
     * @ORM\Column(type="string", name="`add_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=10, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=10, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=10, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $addTime;

    /**
     * @ORM\Column(type="string", name="`update_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=11, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=11, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=11, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $updateTime;

    /**
     * @ORM\Column(type="smallint", name="`state`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=12, align="center", enum=true, dress={0:"default", 1:"processing"}, status=true)
     * @BswAnnotation\Persistence(sort=12, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=12, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Mixed(order=true)
     */
    protected $state = 1;
}