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
 * @ORM\Entity(repositoryClass="Leon\BswBundle\Repository\BswAdminPersistenceLogRepository")
 */
class BswAdminPersistenceLog extends FoundationEntity
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
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="`table`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=64, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=2, render=BswAbs::HTML_TEXT)
     * @BswAnnotation\Persistence(sort=2)
     * @BswAnnotation\Filter(sort=2)
     */
    protected $table;

    /**
     * @ORM\Column(type="integer", name="`user_id`")
     * @Assert\Type(type="integer", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=3, align="center")
     * @BswAnnotation\Persistence(sort=3, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=3, type=BswForm\Number::class)
     */
    protected $userId;

    /**
     * @ORM\Column(type="smallint", name="`type`")
     * @Assert\Type(type="integer", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=4, align="center", enum=true, dress="blue")
     * @BswAnnotation\Persistence(sort=4, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=4, type=BswForm\Select::class, enum=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", name="`before`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=5, width=400, hook={BswHook\JsonStringify::class: {"space": "2"}}, render=BswAbs::HTML_JSON)
     * @BswAnnotation\Persistence(sort=5, hook={0:BswHook\JsonStringify::class}, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=5)
     */
    protected $before;

    /**
     * @ORM\Column(type="string", name="`later`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=6, width=400, hook={BswHook\JsonStringify::class: {"space": "2"}}, render=BswAbs::HTML_JSON)
     * @BswAnnotation\Persistence(sort=6, hook={0:BswHook\JsonStringify::class}, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=6)
     */
    protected $later;

    /**
     * @ORM\Column(type="string", name="`effect`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=7, width=400, hook={BswHook\JsonStringify::class: {"space": "2"}}, render=BswAbs::HTML_JSON)
     * @BswAnnotation\Persistence(sort=7, hook={0:BswHook\JsonStringify::class}, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=7)
     */
    protected $effect;

    /**
     * @ORM\Column(type="string", name="`add_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=8, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=8, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=8, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $addTime;

    /**
     * @ORM\Column(type="string", name="`update_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=9, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=9, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=9, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $updateTime;

    /**
     * @ORM\Column(type="smallint", name="`state`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=10, align="center", enum=true, dress={0:"default", 1:"processing"}, status=true)
     * @BswAnnotation\Persistence(sort=10, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=10, type=BswForm\Select::class, enum=true)
     */
    protected $state = 1;
}