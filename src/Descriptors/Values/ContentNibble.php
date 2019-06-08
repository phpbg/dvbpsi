<?php


namespace PhpBg\DvbPsi\Descriptors\Values;


class ContentNibble
{
    const NIBBLES = [
        0x1 => [
            0x0 => "movie/drama",
            0x1 => "detective/thriller",
            0x2 => "adventure/western/war",
            0x3 => "science fiction/fantasy/horror",
            0x4 => "comedy",
            0x5 => "soap/melodrama/folkloric",
            0x6 => "romance",
            0x7 => "serious/classical/religious/historical movie/drama",
            0x8 => "adult movie/drama",
        ],
        0x2 => [
            0x0 => "news/current affairs",
            0x1 => "news/weather report",
            0x2 => "news magazine",
            0x3 => "documentary",
            0x4 => "discussion/interview/debate",
        ],
        0x3 => [
            0x0 => "show/game show",
            0x1 => "game show/quiz/contest",
            0x2 => "variety show",
            0x3 => "talk show",
        ],
        0x4 => [
            0x0 => "sports",
            0x1 => "special events", //(Olympic Games, World Cup, etc.)
            0x2 => "sports magazines",
            0x3 => "football/soccer",
            0x4 => "tennis/squash",
            0x5 => "team sports", //(excluding football)
            0x6 => "athletics",
            0x7 => "motor sport",
            0x8 => "water sport",
            0x9 => "winter sports",
            0xA => "equestrian",
            0xB => "martial sports",
        ],
        0x5 => [
            0x0 => "children's/youth programmes",
            0x1 => "pre-school children's programmes",
            0x2 => "entertainment programmes for 6 to14",
            0x3 => "entertainment programmes for 10 to 16",
            0x4 => "informational/educational/school programmes",
            0x5 => "cartoons/puppets",
        ],
        0x6 => [
            0x0 => "music/ballet/dance",
            0x1 => "rock/pop",
            0x2 => "serious music/classical music",
            0x3 => "folk/traditional music",
            0x4 => "jazz",
            0x5 => "musical/opera",
            0x6 => "ballet",
        ],
        0x7 => [
            0x0 => "arts/culture", //(without music, general)
            0x1 => "performing arts",
            0x2 => "fine arts",
            0x3 => "religion",
            0x4 => "popular culture/traditional arts",
            0x5 => "literature",
            0x6 => "film/cinema",
            0x7 => "experimental film/video",
            0x8 => "broadcasting/press",
            0x9 => "new media",
            0xA => "arts/culture magazines",
            0xB => "fashion",
        ],
        0x8 => [
            0x0 => "social/political issues/economics",
            0x1 => "magazines/reports/documentary",
            0x2 => "economics/social advisory",
            0x3 => "remarkable people",
        ],
        0x9 => [
            0x0 => "education/science/factual topics",
            0x1 => "nature/animals/environment",
            0x2 => "technology/natural sciences",
            0x3 => "medicine/physiology/psychology",
            0x4 => "foreign countries/expeditions",
            0x5 => "social/spiritual sciences",
            0x6 => "further education",
            0x7 => "languages",
        ],
        0xA => [
            0x0 => "leisure hobbies",
            0x1 => "tourism/travel",
            0x2 => "handicraft",
            0x3 => "motoring",
            0x4 => "fitness and health",
            0x5 => "cooking",
            0x6 => "advertisement/shopping",
            0x7 => "gardening",
        ],
        0xB => [
            0x0 => "original language",
            0x1 => "black and white",
            0x2 => "unpublished",
            0x3 => "live broadcast",
            0x4 => "plano-stereoscopic",
            0x5 => "local or regional",
        ],
    ];
}