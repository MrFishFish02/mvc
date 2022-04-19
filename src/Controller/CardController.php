<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CardController extends AbstractController
{
    private $deck;

    #[Route('/card', name: 'app_card')]
    public function index(SessionInterface $session): Response
    {   
        if (!$session->has("deck")) {
            $session->set("deck", new deck);
            $session->get("deck")->create_deck();
        }
        /** Creates a deck when you access the route */
        return $this->render('card/index.html.twig', [
            'controller_name' => 'CardController',
            'test' =>  $session->get('deck'),
        ]);
    }

    /** -------------------SHOW DECK----------------------- */
    #[Route('/card/deck', name: 'card_deck')]
    public function deck(SessionInterface $session): Response
    {
        $session->get("deck")->sort_deck();
        return $this->render('card/deck.html.twig', [
            'controller_name' => 'deck_controller',
            'all' => $session->get("deck")->get_deck_all(),
        ]);
    }

    /** -------------------DRAW----------------------- */
    #[Route('/card/deck/draw/:{amount}/', name: 'deck_draw')]
    public function draw(SessionInterface $session, int $amount = 1): Response
    {   $aaa = $session->get('deck')->get_remaining_cards();

        if ($amount) {
            $session->get('deck')->draw_number($amount);
        }
        return $this->render('card/draw.html.twig', [
            'controller_name' => 'draw_card',
            'before_draw' => $aaa,
            'cards_' => $session->get('deck')->get_draws(),
            'remaining_cards' => $session->get('deck')->get_remaining_cards(),
        ]);
    }

    /** -------------------DECK----------------------- */
    #[Route('/card/deck/shuffle', name: 'deck_shuffle')]
    public function shuffle_deck(SessionInterface $session): Response
    {
        return $this->render('card/shuffle.html.twig', [
            'controller_name' => 'deck_shuffle',
            'full_deck' => $session->get('deck')->shuffle_deck(),
        ]);
    }

    /** -------------------player----------------------- */
    #[Route('/card/deck/deal/:{players}/:{cards}/', name: 'player_cards')]
    public function player(SessionInterface $session, int $players = 2, int $cards = 3): Response
    {
        $temp = [];
        for ($i = 0; $i < $players; $i++) {
            $temp[] = $session->get('deck')->draw_number($cards, true);
        }
        return $this->render('card/players.html.twig', [
            'controller_name' => 'deck_shuffle',
            'temp' => $temp,
            'remaining_cards' => $session->get('deck')->get_remaining_cards(),
        ]);
    } 

    /** -------------------SHOW DECK2----------------------- */
    #[Route('/card/deck2', name: 'card_deck2')]
    public function deck2(SessionInterface $session): Response
    {
        $test = new DeckWith2Jokers;
        $test->create_deck();
        $test->add_card("Joker", "Joker", "black", "NULL");
        $test->add_card("Joker", "Joker", "red", "NULL");
        $test->sort_deck();
        return $this->render('card/deck.html.twig', [
            'controller_name' => 'deck_controller',
            'all' => $test->get_deck_all(),
        ]);
    }


     
    /** -------------------SHOW API_DECK----------------------- */
    #[Route('/card/api/deck', name: 'api_deck', methods: ['GET', 'POST'])]
    public function api_deck(SessionInterface $session): Response
    {
    $session->get('deck')->sort_deck();
    return new JsonResponse([
        'deck' => $session->get('deck')->get_deck_all(),
    ]);
    }

    /** -------------------SHOW API_SHUFFLE_DECK----------------------- */
    #[Route('/card/api/deck/shuffle', name: 'api_deck_shuffle', methods: ['GET', 'POST'])]
    public function api_shuffle(SessionInterface $session): Response
    {
    
    return new JsonResponse([
        'deck' => $session->get('deck')->shuffle_deck(),
    ]);
    }

    /** -------------------DRAW API DRAW----------------------- */
    #[Route('/card/api/deck/draw/:{amount}/', name: 'deck_draw_api', methods: ['GET', 'POST'])]
    public function draw_api(SessionInterface $session, int $amount = 1): Response
    {
        if ($amount) {$session->get('deck')->draw_number($amount);};
        return new JsonResponse([
            'remaining' => $session->get('deck')->get_remaining_cards(), 
            'draws' => $session->get('deck')->get_draws(),
        ]);
    }

    #[Route('/card/api/deck/deal/:{players}/:{cards}/', name: 'deck_players_api', methods: ['POST', 'GET'])]
    public function players_api(SessionInterface $session, int $players = 2, int $cards = 3): Response
    {
        $temp = [];
        for ($i = 0; $i < $players; $i++) {
            $temp[] = $session->get('deck')->draw_number($cards, true);
        }
        return new JsonResponse([
            'remaining' => $session->get('deck')->get_remaining_cards(), 
            'players_cards' => $temp,
        ]);
    }
}

class card
{
    public function create_card($value, $symbol, $color, $points){
        $this->card = [$value, $symbol, $color, $points];
    }

    public function get_card(){
        return $this->card;
    }
}

class deck
{
    public $draws = [];
    public $temp_draws;
    public function create_deck() {
        $color = ["black", "black", "red", "red"];
        $symbol = ["♠", "♣", "♥", "♦"];
        $number = ["A", "2", "3", "4", "5", "6", "7", "8", "9","10", "J",  "Q", "K"];
        $this->deck = [];
        for ($i = 0; $i < 13; $i++) {
            for ($b = 0; $b < 4; $b++) {
                $card = new card;
                $card->create_card($number[$i], $symbol[$b], $color[$b], $i + 1);
                array_push($this->deck, $card->get_card());
            }
        }
    }

    public function get_deck_all() {
        return $this->deck;
    }

    public function get_spec($specification) {
        $this->spec_deck = [];
        forEach ($this->get_deck_all() as $key) {
            if (in_array($specification, $key)) {
                array_push($this->spec_deck, $key);
            }
        }
        return $this->special_sort($this->spec_deck);
    }

    public function draw_number(int $length = 0, $bool = false) {
        $this->draws_index = [];
        $this->temp_draws = [];
        for ($i = 0; $i < $length; $i++) {
            $range = rand(0, count($this->get_deck_all()) - 1);
            $this->temp_draws[] = $this->deck[$range];
            array_push($this->draws, array_splice($this->deck, $range, 1));
        }

        if ($bool) { return $this->temp_draws; }
    }

    public function get_draws() {
        /** For debug, returns draw index from deck */
        return $this->draws;
    }

    public function get_remaining_cards() {
        /** return remaining cards */
        return count($this->deck);
    }

    public function shuffle_deck() {
        /** This function shuffles the deck and returns the deck */
        $this->deck = [];
        $this->draws = [];
        $this->create_deck();
        shuffle($this->deck);
        return $this->deck;
    }

    function special_sort($array) {
        $order = ["A", "2", "3", "4", "5", "6", "7", "8", "9","10", "J",  "Q", "K"];
        usort($array, function($a, $b) use ($order) {
            $pos_one = array_search($a[0], $order);
            $pos_two = array_search($b[0], $order);
            return $pos_one - $pos_two;
        });
        return $array;
    }

    function sort_deck() {
        $sort = array();
        foreach($this->deck as $k=>$v) {
            $sort[2][$k] = $v[2];
            $sort[1][$k] = $v[1];
            $sort[3][$k] = $v[3];
        }
        # sort by event_type desc and then title asc

        array_multisort($sort[2], SORT_DESC, $sort[1], $sort[3],SORT_ASC,$this->deck);
    }
    /**
     *
     * Convert an object to an array
     *
     * @param number - value such as A, 5, K...
     * @param symbol - symbol ["♠", "♣", "♥", "♦"]
     * @param color - color of the text
     *
    */
    public function add_card($number, $symbol, $color, $points) {
        $card = new card;
        $card->create_card($number, $symbol, $color, $points);
        array_push($this->deck, $card->get_card());
    }
}

class DeckWith2Jokers extends Deck{}