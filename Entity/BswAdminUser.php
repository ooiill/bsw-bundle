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
 * @ORM\Entity(repositoryClass="Leon\BswBundle\Repository\BswAdminUserRepository")
 * @UniqueEntity(fields="phone", errorPath="phone", message="Record exists.", groups={"modify", "newly"})
 */
class BswAdminUser extends FoundationEntity
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
     * @ORM\Column(type="string", name="`phone`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=16, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=2, align="center", render=BswAbs::RENDER_CODE, label="Account")
     * @BswAnnotation\Persistence(sort=2, label="Account")
     * @BswAnnotation\Filter(sort=2, label="Account")
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", name="`name`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=32, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=3, render=BswAbs::HTML_TEXT, width=200, align="center")
     * @BswAnnotation\Persistence(sort=3)
     * @BswAnnotation\Filter(sort=3)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="`password`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=32, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=4, render=BswAbs::HTML_TEXT, show=false)
     * @BswAnnotation\Persistence(sort=4, show=false, ignoreBlank=true)
     * @BswAnnotation\Filter(sort=4, show=false)
     */
    protected $password;

    /**
     * @ORM\Column(type="integer", name="`role_id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=5, align="center", enumExtra=true)
     * @BswAnnotation\Persistence(sort=5, type=BswForm\Select::class, enumExtra=true)
     * @BswAnnotation\Filter(sort=5, type=BswForm\Select::class, enumExtra=true)
     */
    protected $roleId = 0;

    /**
     * @ORM\Column(type="integer", name="`team_id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=6, align="center", enumExtra=true)
     * @BswAnnotation\Persistence(sort=6, type=BswForm\Select::class, enumExtra=true)
     * @BswAnnotation\Filter(sort=6, type=BswForm\Select::class, enumExtra=true)
     */
    protected $teamId = 0;

    /**
     * @ORM\Column(type="smallint", name="`team_leader`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=7, align="center", width=140, enum=BswEnum::OPPOSE, dress={0:"orange",1:"blue"})
     * @BswAnnotation\Persistence(sort=7, type=BswForm\Select::class, enum=BswEnum::OPPOSE)
     * @BswAnnotation\Filter(sort=7, type=BswForm\Select::class, enum=BswEnum::OPPOSE)
     */
    protected $teamLeader = 0;

    /**
     * @ORM\Column(type="smallint", name="`sex`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=8, align="center", width=140, enum=true, dress="blue")
     * @BswAnnotation\Persistence(sort=8, type=BswForm\Radio::class, enum=true)
     * @BswAnnotation\Filter(sort=8, type=BswForm\Select::class, enum=true)
     */
    protected $sex = 0;

    /**
     * @ORM\Column(type="bigint", name="`telegram_id`")
     * @Assert\Type(type="numeric", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=9, align="center", width=150)
     * @BswAnnotation\Persistence(sort=9, type=BswForm\Number::class, typeArgs={"min":-9.2233720368548E+18, "max":9.2233720368548E+18})
     * @BswAnnotation\Filter(sort=9, type=BswForm\Number::class, typeArgs={"min":-9.2233720368548E+18, "max":9.2233720368548E+18})
     */
    protected $telegramId = 0;

    /**
     * @ORM\Column(type="string", name="`google_auth_secret`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=16, groups={"modify"})
     * @BswAnnotation\Preview(sort=10, align="center", render=BswAbs::RENDER_SECRET, width=150)
     * @BswAnnotation\Persistence(sort=10, show=false, ignoreBlank=true)
     * @BswAnnotation\Filter(sort=10, show=false)
     */
    protected $googleAuthSecret = "";

    /**
     * @ORM\Column(type="integer", name="`avatar_attachment_id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=11, align="center")
     * @BswAnnotation\Persistence(sort=11, type=BswForm\Upload::class, typeArgs={"flag":"bsw-admin-user", "fileMd5Key":"md5", "fileSha1Key":"sha1"}, disabledOverall=false)
     * @BswAnnotation\Filter(sort=11, type=BswForm\Number::class)
     */
    protected $avatarAttachmentId = 0;

    /**
     * @ORM\Column(type="string", name="`add_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=12, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=12, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=12, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $addTime;

    /**
     * @ORM\Column(type="string", name="`update_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=13, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=13, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=13, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $updateTime;

    /**
     * @ORM\Column(type="smallint", name="`state`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=14, align="center", enum=true, dress={0:"default", 1:"processing"}, status=true)
     * @BswAnnotation\Persistence(sort=14, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=14, type=BswForm\Select::class, enum=true)
     */
    protected $state = 1;
}