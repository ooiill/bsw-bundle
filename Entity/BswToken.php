<?php

namespace Leon\BswBundle\Entity;

use Leon\BswBundle\Entity\FoundationEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Leon\BswBundle\Annotation\Entity as BswAnnotation;
use Leon\BswBundle\Module\Entity\Abs as BswAbs;
use Leon\BswBundle\Module\Entity\Enum as BswEnum;
use Leon\BswBundle\Module\Hook\Entity as BswHook;
use Leon\BswBundle\Module\Form\Entity as BswForm;
use Leon\BswBundle\Module\Filter\Entity as BswFilter;
use Leon\BswBundle\Component\Helper as BswHelper;

/**
 * @ORM\Entity(repositoryClass="Leon\BswBundle\Repository\BswTokenRepository")
 * @UniqueEntity(fields="token", errorPath="token", message="Record exists", groups={"modify", "newly"})
 */
class BswToken extends FoundationEntity
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
     * @ORM\Column(type="integer", name="`user_id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=2, align="center")
     * @BswAnnotation\Persistence(sort=2, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=2, type=BswForm\Number::class)
     */
    protected $userId = 0;

    /**
     * @ORM\Column(type="smallint", name="`scene`")
     * @Assert\Type(type="integer", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=3, align="center", enum=true, dress="blue")
     * @BswAnnotation\Persistence(sort=3, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=3, type=BswForm\Select::class, enum=true)
     */
    protected $scene;

    /**
     * @ORM\Column(type="string", name="`token`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=64, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=4, render=BswAbs::HTML_TEXT)
     * @BswAnnotation\Persistence(sort=4)
     * @BswAnnotation\Filter(sort=4)
     */
    protected $token;

    /**
     * @ORM\Column(type="integer", name="`effective_times`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=5, align="center", render=BswAbs::RENDER_CODE)
     * @BswAnnotation\Persistence(sort=5, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=5, type=BswForm\Number::class)
     */
    protected $effectiveTimes = 1;

    /**
     * @ORM\Column(type="integer", name="`expires_time`")
     * @Assert\Type(type="integer", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=6, align="center", render=BswAbs::RENDER_CODE, hook={0:BswHook\Timestamp::class}, width=190)
     * @BswAnnotation\Persistence(sort=6, type=BswForm\Datetime::class, hook={0:BswHook\Timestamp::class})
     * @BswAnnotation\Filter(sort=6, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class, filterArgs={"timestamp":true})
     */
    protected $expiresTime;

    /**
     * @ORM\Column(type="string", name="`params`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=7, width=360, hook={0:BswHook\JsonStringify::class}, render=BswAbs::HTML_JSON)
     * @BswAnnotation\Persistence(sort=7, hook={0:BswHook\JsonStringify::class}, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=7)
     */
    protected $params;

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