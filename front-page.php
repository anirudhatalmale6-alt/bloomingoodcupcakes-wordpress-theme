<?php
/**
 * The home page.
 *
 * One page, in the order the approved redesign put things: what it is, why you
 * can trust the kitchen, what it costs, who makes it, what you need to know,
 * where you collect it, what other people said, and then the form. The order
 * is the argument -- the hygiene rating comes second because it is the first
 * thing a stranger worries about when food is made in somebody's house.
 *
 * Markup follows the mockup class for class. The stylesheet came from the
 * mockup, so a tidier class name here is simply an unstyled one.
 *
 * @package bloomingood
 */

get_header();

?>

<section class="hero">
	<div class="hero-copy rise">
		<?php if ( bgc_opt( 'eyebrow' ) ) : ?>
			<p class="eyebrow"><?php echo esc_html( bgc_opt( 'eyebrow' ) ); ?></p>
		<?php endif; ?>
		<h1><?php esc_html_e( 'Bespoke cupcake bouquets, made to order in Hethersett, Norwich', 'bloomingood' ); ?></h1>
		<p class="hero-sub"><?php esc_html_e( 'Cupcakes and party cakes, baked fresh to order in my home kitchen in Norwich. Each bite is a celebration of quality, creativity, and passion.', 'bloomingood' ); ?></p>
		<div class="hero-acts">
			<a class="btn btn-lg" href="#order"><?php esc_html_e( 'Start your order', 'bloomingood' ); ?></a>
			<a class="tlink" href="#bouquets"><span><?php esc_html_e( 'See the price list', 'bloomingood' ); ?></span></a>
		</div>
		<p class="hero-helper"><?php esc_html_e( 'Tell me what you are planning and I will come back to you. You do not need to know the size yet.', 'bloomingood' ); ?></p>
		<p class="hero-trust">
			<span class="pip"><?php echo esc_html( bgc_opt( 'fhrs_rating' ) ); ?></span>
			<?php
			printf(
				/* translators: 1: rating, 2: awarding authority, 3: date */
				esc_html__( 'Food hygiene rating %1$s, awarded by %2$s on %3$s.', 'bloomingood' ),
				esc_html( bgc_opt( 'fhrs_rating' ) ),
				esc_html( bgc_opt( 'fhrs_authority' ) ),
				esc_html( bgc_opt( 'fhrs_date' ) )
			);
			?>
		</p>
	</div>
	<div class="hero-img">
		<?php
		bgc_picture(
			'hero-bouquet-19',
			__( 'A nineteen cupcake bouquet, each cupcake piped as a flower in pinks, cream and lilac, wrapped in paper', 'bloomingood' ),
			array(
				'sizes'         => '(min-width:900px) 46vw, 100vw',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decoding'      => 'sync',
			)
		);
		?>
	</div>
</section>

<section class="band band-blush" id="hygiene">
	<div class="shell">
		<div class="cred">
			<div class="cred-cert">
				<?php
				bgc_picture(
					'hygiene-cert',
					__( 'The Food Standards Agency food hygiene rating certificate displayed in the window, showing a rating of 5, very good', 'bloomingood' ),
					array( 'sizes' => '(min-width:840px) 40vw, 100vw' )
				);
				?>
			</div>
			<div class="cred-body">
				<h2 class="h2"><?php esc_html_e( 'Baked in my own kitchen, inspected and rated', 'bloomingood' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: authority, 2: date, 3: rating */
						esc_html__( '%1$s inspected my kitchen on %2$s and gave it a food hygiene rating of %3$s, the top band on the Food Standards Agency scale. You can look the rating up on the register yourself.', 'bloomingood' ),
						esc_html( bgc_opt( 'fhrs_authority' ) ),
						esc_html( bgc_opt( 'fhrs_date' ) ),
						esc_html( bgc_opt( 'fhrs_rating' ) )
					);
					?>
				</p>
				<?php if ( bgc_opt( 'fhrs_url' ) ) : ?>
					<p class="cred-link">
						<a class="tlink" href="<?php echo esc_url( bgc_opt( 'fhrs_url' ) ); ?>" rel="noopener">
							<span><?php esc_html_e( 'See the register entry', 'bloomingood' ); ?></span>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<div class="promises">
			<div class="promise">
				<h3 class="h3"><?php esc_html_e( '100% Hand Made', 'bloomingood' ); ?></h3>
				<p><?php esc_html_e( 'Every cake is individually designed, hand-piped, and finished with care. No shortcuts, no mass production, just artistry, precision, and a personal touch in every detail.', 'bloomingood' ); ?></p>
			</div>
			<div class="promise">
				<h3 class="h3"><?php esc_html_e( 'Made Fresh To Order', 'bloomingood' ); ?></h3>
				<p><?php esc_html_e( 'Your cakes are never sitting on a shelf. Each one is baked only when you order, ensuring the perfect balance of flavour, freshness, and presentation, so your special moments taste as good as they look.', 'bloomingood' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="band band-tall" id="bouquets">
	<div class="shell">
		<h2 class="h2"><?php esc_html_e( 'Cupcake Bouquets and Boxes', 'bloomingood' ); ?></h2>
		<p class="lede"><?php esc_html_e( 'I design beautiful buttercream floral bouquets and gift boxes, featuring hand-piped flowers in any colour. Perfect for Mother\'s Day, birthdays, anniversaries, engagements, and baby showers, these edible creations make every occasion extra special. The cupcakes are available in vanilla or chocolate.', 'bloomingood' ); ?></p>
		<div class="zig">
			<div class="zrow">
				<div class="zshot">
					<?php bgc_picture( 'bouquet-7-red', __( 'A bouquet of seven deluxe cupcakes piped as deep red roses and cream chrysanthemums with small pumpkins', 'bloomingood' ) ); ?>
				</div>
				<div class="zbody">
					<h3 class="h3"><?php esc_html_e( 'Bouquets', 'bloomingood' ); ?></h3>
					<p><?php esc_html_e( 'Hand-piped flowers in any colour, wrapped and ready to hand over.', 'bloomingood' ); ?></p>
					<?php bgc_price_list( 'prices_bouquet' ); ?>
				</div>
			</div>
			<div class="zrow zrow-flip">
				<div class="zshot">
					<?php bgc_picture( 'box-12-pink', __( 'Twelve deluxe cupcakes in a white box, piped as pale pink and cream flowers, photographed from above', 'bloomingood' ) ); ?>
				</div>
				<div class="zbody">
					<h3 class="h3"><?php esc_html_e( 'Boxes', 'bloomingood' ); ?></h3>
					<p><?php esc_html_e( 'The same piping, presented in a gift box.', 'bloomingood' ); ?></p>
					<?php bgc_price_list( 'prices_box' ); ?>
				</div>
			</div>
			<div class="zrow">
				<div class="zshot">
					<?php bgc_picture( 'box-6-red-yellow', __( 'Six cupcakes piped as red roses and yellow chrysanthemums in a box, showing colours other than pink', 'bloomingood' ) ); ?>
				</div>
				<div class="zbody">
					<h3 class="h3"><?php esc_html_e( 'Any colour you like', 'bloomingood' ); ?></h3>
					<p><?php esc_html_e( 'The flowers are piped to match whatever you are planning, not to a fixed palette.', 'bloomingood' ); ?></p>
					<p class="cred-link"><a class="tlink" href="#order"><span><?php esc_html_e( 'Start your order', 'bloomingood' ); ?></span></a></p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="band band-tall" id="cakes">
	<div class="shell">
		<h2 class="h2"><?php esc_html_e( 'Party Cakes', 'bloomingood' ); ?></h2>
		<p class="lede"><?php esc_html_e( 'I also create stunning Naked Cakes and indulgent Overload Drip Cakes, made to order in a variety of sizes. Choose from popular flavours like vanilla, chocolate, lemon, Biscoff, or Oreo, or request something bespoke to suit your celebration.', 'bloomingood' ); ?></p>
		<div class="cakes">
			<div class="cake">
				<div class="cake-shot">
					<?php bgc_picture( 'cake-naked', __( 'A three layer naked cake with visible sponge layers, topped with green and white piped flowers', 'bloomingood' ), array( 'sizes' => '(min-width:800px) 45vw, 100vw' ) ); ?>
				</div>
				<h3 class="h3"><?php esc_html_e( 'Naked Cakes', 'bloomingood' ); ?></h3>
				<?php bgc_price_list( 'prices_naked' ); ?>
			</div>
			<div class="cake">
				<div class="cake-shot">
					<?php bgc_picture( 'cake-drip', __( 'A three layer drip cake with pink icing running down the sides, topped with biscuits, wafers and piped swirls', 'bloomingood' ), array( 'sizes' => '(min-width:800px) 45vw, 100vw' ) ); ?>
				</div>
				<h3 class="h3"><?php esc_html_e( 'Overload Drip Cakes', 'bloomingood' ); ?></h3>
				<?php bgc_price_list( 'prices_drip' ); ?>
			</div>
		</div>
	</div>
</section>

<section class="band band-tall" id="weddings">
	<div class="shell">
		<h2 class="h2"><?php esc_html_e( 'Weddings', 'bloomingood' ); ?></h2>
		<div class="wed">
			<div>
				<blockquote class="wed-quote"><?php esc_html_e( 'Bloomin\' Good Cupcakes supplied 70 cupcakes for our wedding, including vegan and gluten free options. The cakes were absolutely delicious and stunningly beautiful! They made a great alternative from the traditional wedding cake, and made it straightforward to cater to different dietary requirements. Photo credit Emma Louise Photography.', 'bloomingood' ); ?></blockquote>
				<cite class="wed-attr"><?php esc_html_e( 'Bryony Yates · Google review', 'bloomingood' ); ?></cite>
				<p class="wed-note"><?php esc_html_e( 'Wedding orders start with a consultation, where we agree the design and the deposit together.', 'bloomingood' ); ?></p>
				<p class="cred-link"><a class="tlink" href="#order"><span><?php esc_html_e( 'Ask about a wedding', 'bloomingood' ); ?></span></a></p>
			</div>
			<div class="wed-shots">
				<div><?php bgc_picture( 'wedding-bouquet', __( 'A wedding cupcake bouquet in dusky pink and cream, piped as roses and dahlias', 'bloomingood' ), array( 'sizes' => '(min-width:880px) 24vw, 50vw' ) ); ?></div>
				<div><?php bgc_picture( 'wedding-12-roses', __( 'A bouquet of twelve cupcakes piped as pink roses and cream daisies, wrapped in paper', 'bloomingood' ), array( 'sizes' => '(min-width:880px) 24vw, 50vw' ) ); ?></div>
			</div>
		</div>
	</div>
</section>

<section class="band band-tall" id="about">
	<div class="shell">
		<div class="about">
			<div class="about-portrait">
				<?php bgc_picture( 'claire-portrait', __( 'Claire Broom, who bakes and pipes every order, sitting outdoors in a garden', 'bloomingood' ), array( 'sizes' => '(min-width:880px) 38vw, 100vw' ) ); ?>
			</div>
			<div class="about-body">
				<h2 class="h2"><?php esc_html_e( 'About Me', 'bloomingood' ); ?></h2>
				<p><?php esc_html_e( 'Baking has always been more than a hobby, it\'s my passion, my comfort, and my creative escape. What began as late-night kitchen experiments has grown into a dream come true: creating handcrafted cupcakes and party cakes, that bring joy to every celebration.', 'bloomingood' ); ?></p>
				<p><?php esc_html_e( 'From the very first swirl of frosting to the delicate finishing touches on our beautifully wrapped gift boxes, I dedicate care, creativity, and attention to every single detail. I am truly grateful for the opportunity to be a small, sweet part of your most joyful celebrations.', 'bloomingood' ); ?></p>
				<p><?php esc_html_e( 'Every dessert is made from scratch using only the finest ingredients and time-tested recipes.', 'bloomingood' ); ?></p>
				<p class="about-sign"><?php esc_html_e( 'Claire x', 'bloomingood' ); ?></p>
				<p class="about-role"><?php esc_html_e( 'Claire Broom, Bloomin\' Good Cupcakes', 'bloomingood' ); ?></p>
				<p class="about-mission"><?php esc_html_e( 'To make life a little sweeter, one dessert at a time.', 'bloomingood' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="band band-tall" id="know">
	<div class="shell">
		<h2 class="h2"><?php esc_html_e( 'What to know before you order', 'bloomingood' ); ?></h2>
		<p class="lede"><?php esc_html_e( 'The answers people usually write in to ask, in one place.', 'bloomingood' ); ?></p>
		<dl class="know">
			<?php
			/*
			 * These four were published only on the terms and conditions page of
			 * the old site, which is where a buyer never looks. The nut statement
			 * in particular reads as somebody who knows how her own kitchen works
			 * -- next to the products it is reassurance, in the small print it is
			 * a disclaimer.
			 */
			$bgc_know = array(
				array(
					__( 'Allergies', 'bloomingood' ),
					__( 'Any allergies must be declared at the time of placing your order. While none of the ingredients I use in your order contain nuts or peanuts, nuts are handled in my kitchen. Despite the utmost care being taken, I cannot guarantee that any product will be completely free from traces of nuts or peanuts.', 'bloomingood' ),
				),
				array(
					__( 'Payment', 'bloomingood' ),
					__( 'All payments are taken in full. All orders are made to order.', 'bloomingood' ),
				),
				array(
					__( 'Keeping them', 'bloomingood' ),
					__( 'Due to the perishable nature of these products, I do not recommend keeping longer than three days. Refrigeration can alter the flavour and texture of the product. Store in a cool, dry place if necessary.', 'bloomingood' ),
				),
				array(
					__( 'Colour', 'bloomingood' ),
					__( 'Due to the nature of these products I cannot always guarantee 100% uniformity in colours from order to order, separate batches may vary slightly, however I always endeavour to keep consistency.', 'bloomingood' ),
				),
			);
			foreach ( $bgc_know as $bgc_row ) :
				?>
				<div class="kitem">
					<dt><?php echo esc_html( $bgc_row[0] ); ?></dt>
					<dd><?php echo esc_html( $bgc_row[1] ); ?></dd>
				</div>
				<?php
			endforeach;
			?>
		</dl>
	</div>
</section>

<section class="band band-blush" id="collection">
	<div class="shell">
		<div class="coll">
			<div class="coll-body">
				<h2 class="h2"><?php esc_html_e( 'Collection from my kitchen in Hethersett', 'bloomingood' ); ?></h2>
				<p><?php esc_html_e( 'Baked fresh to order, artfully hand-piped, and elegantly presented, your cakes are prepared with the finest care and ready for pickup at their most irresistible.', 'bloomingood' ); ?></p>
				<ul class="coll-facts" role="list">
					<li>
						<span class="cf-k"><?php esc_html_e( 'Opening hours', 'bloomingood' ); ?></span>
						<span class="cf-v"><?php echo esc_html( bgc_opt( 'hours' ) ); ?></span>
					</li>
					<li>
						<span class="cf-k"><?php esc_html_e( 'Getting here', 'bloomingood' ); ?></span>
						<span class="cf-v"><?php esc_html_e( 'Hethersett is just under six miles southwest of Norwich.', 'bloomingood' ); ?></span>
					</li>
				</ul>
				<p class="cred-link">
					<a class="tlink" href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode( get_bloginfo( 'name' ) . ' ' . bgc_opt( 'address' ) ); ?>" rel="noopener">
						<span><?php esc_html_e( 'Find Bloomin\' Good Cupcakes on Google', 'bloomingood' ); ?></span>
					</a>
				</p>
			</div>
			<div class="panel">
				<span class="panel-motif">
					<img src="<?php echo esc_url( BGC_URI . '/assets/img/panel-motif.jpg' ); ?>" alt="" loading="lazy" width="120" height="120">
				</span>
				<p class="panel-label"><?php esc_html_e( 'COLLECTION POINT', 'bloomingood' ); ?></p>
				<p class="panel-place"><?php esc_html_e( 'Hethersett, Norwich', 'bloomingood' ); ?></p>
				<p class="panel-addr"><?php echo esc_html( bgc_opt( 'address' ) ); ?></p>
				<p class="panel-dist"><?php esc_html_e( 'Just under six miles southwest of Norwich city centre.', 'bloomingood' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="band band-tall" id="customers">
	<div class="shell">
		<h2 class="h2"><?php esc_html_e( 'Happy Customers', 'bloomingood' ); ?></h2>
		<?php
		/*
		 * Live Google reviews if there are enough of them to be worth showing,
		 * otherwise the written quotes. See inc/reviews.php for why the floor
		 * exists: as of the audit this listing carried one review, and a feed
		 * showing one review advertises the weakness rather than hiding it.
		 */
		if ( function_exists( 'bgc_reviews_ready' ) && bgc_reviews_ready() ) :
			$bgc_g = bgc_google_reviews();
			?>
			<p class="lede">
				<?php
				printf(
					/* translators: 1: star rating, 2: number of reviews */
					esc_html__( 'Rated %1$s on Google from %2$s reviews.', 'bloomingood' ),
					esc_html( number_format_i18n( $bgc_g['rating'], 1 ) ),
					esc_html( number_format_i18n( $bgc_g['count'] ) )
				);
				?>
			</p>
			<div class="quotes">
				<?php foreach ( array_slice( $bgc_g['reviews'], 0, 4 ) as $bgc_r ) : ?>
					<div class="q">
						<blockquote><?php echo esc_html( $bgc_r['text'] ); ?></blockquote>
						<cite>
							<?php echo esc_html( $bgc_r['author'] ); ?><?php
							if ( $bgc_r['when'] ) {
								echo ' &middot; ' . esc_html( $bgc_r['when'] );
							}
							?>
						</cite>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $bgc_g['url'] ) : ?>
				<p class="gal-foot">
					<a class="tlink" href="<?php echo esc_url( $bgc_g['url'] ); ?>" rel="noopener">
						<span><?php esc_html_e( 'Read them on Google', 'bloomingood' ); ?></span>
					</a>
				</p>
			<?php endif; ?>
		<?php else : ?>
			<div class="quotes">
				<div class="q">
					<blockquote><?php esc_html_e( 'I am unbelievably impressed by the cupcakes you so kindly brought in to us at the surgery today. It was safe to say they tasted as good as they looked! It made everyone smile. Thank you', 'bloomingood' ); ?></blockquote>
					<cite><?php esc_html_e( 'Cupcakes taken in to a local surgery', 'bloomingood' ); ?></cite>
				</div>
				<div class="q">
					<blockquote><?php esc_html_e( 'Claire made an absolutely amazing 30th cupcake birthday cake for my daughter. They really did look like a bunch of flowers and the colours were stunning. They not only looked gorgeous, but they were delicious. If you ever need a birthday cake made, then Bloomin\' Good Cupcakes is definitely the best choice!', 'bloomingood' ); ?></blockquote>
					<cite><?php esc_html_e( 'A 30th birthday, ordered for a daughter', 'bloomingood' ); ?></cite>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="band band-tall" id="order">
	<div class="shell">
		<div class="order-grid">
			<div>
				<h2 class="h2"><?php esc_html_e( 'Start your order', 'bloomingood' ); ?></h2>
				<p class="lede"><?php esc_html_e( 'Have a cake question or a custom order in mind? I\'d love to hear from you! Whether you\'re planning an event, need help choosing the perfect treat, or just want to say hello, I am here and ready to advise....and bake!', 'bloomingood' ); ?></p>
				<p class="wed-note"><?php esc_html_e( 'Complete this form to ask your question or request a bespoke order, freshly made just for you.', 'bloomingood' ); ?></p>
				<p class="order-alt">
					<?php esc_html_e( 'Prefer to talk it through? Call', 'bloomingood' ); ?>
					<a href="tel:<?php echo esc_attr( bgc_tel_href() ); ?>"><?php echo esc_html( bgc_opt( 'phone' ) ); ?></a>.
				</p>
				<div class="order-shot">
					<?php bgc_picture( 'order-box-6', __( 'Six deluxe cupcakes in a box, piped as a deep red rose and cream flowers', 'bloomingood' ), array( 'sizes' => '(min-width:900px) 40vw, 100vw' ) ); ?>
				</div>
			</div>
			<div class="form-card">
				<?php require BGC_DIR . '/inc/form.php'; ?>
			</div>
		</div>
	</div>
</section>

<section class="band band-tall" id="gallery">
	<div class="shell">
		<h2 class="h2"><?php esc_html_e( 'A Showcase of my creations...', 'bloomingood' ); ?></h2>
		<p class="lede"><?php esc_html_e( 'Just some of my handcrafted cakes and cupcakes, each designed with creativity, elegance, and flavour in mind. From delicate cupcakes to statement party cakes, every bake is lovingly made for birthdays, weddings, and celebrations of every kind, bringing a personal touch to your special occasion.', 'bloomingood' ); ?></p>
		<div class="gal">
			<div class="gal-a"><?php bgc_picture( 'gallery-box-4', __( 'Four deluxe cupcakes in a box, piped as pink rosettes', 'bloomingood' ), array( 'sizes' => '(min-width:800px) 42vw, 100vw' ) ); ?></div>
			<div class="gal-b"><?php bgc_picture( 'gallery-bouquet-7', __( 'A bouquet of seven deluxe cupcakes in deep red and cream', 'bloomingood' ), array( 'sizes' => '(min-width:800px) 28vw, 50vw' ) ); ?></div>
			<div class="gal-c"><?php bgc_picture( 'gallery-6-flowers', __( 'Six deluxe cupcakes piped as pale pink and cream flowers', 'bloomingood' ), array( 'sizes' => '(min-width:800px) 28vw, 50vw' ) ); ?></div>
		</div>
		<?php if ( bgc_opt( 'instagram' ) ) : ?>
			<p class="gal-foot">
				<a class="tlink" href="<?php echo esc_url( bgc_opt( 'instagram' ) ); ?>" rel="noopener">
					<span><?php esc_html_e( 'Follow on Instagram', 'bloomingood' ); ?></span>
				</a>
			</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
