<div id="devone" class="tabcontent" style="display: block;">

    <article class="cp_soft-card">
            <!-- article Formulaires -->
        <header>
        <h1>Formulaires</h1>
        <p>elements de formulaires et mise en oeuvre</p>
        </header>
        
        <section>
            <h2>Controles</h2>
            <div>
            <div>
                <h3>form mot</h3>

                    <form id="motForm"  onsubmit="return validateForm(this)">
                        <label for="idInput">ID (optionnel) :</label><br />
                        <input type="number" id="idInput" name="motid" min="1" /><br />

                        <label for="qInput">Mot (optionnel) :</label><br />
                        <input type="text" id="qInput" name="motq" /><br />

                        <input type="submit" value="Submit">
                        <div id="result"></div>  
                    </form>

                                
                
            </div>
            <aside>
                Les controles elementaires font intervenir 2 à 3 elements
                la structure
                le style
                les scripts
            </aside>
            </div>
        </section>

    </article>     

</div>
<!-- 
  <dialog id="DIALOG_1" class="cp_dialog">
    <button autofocus onclick="closeModal('DIALOG_1')">Fermer</button>
    <p>Cette boîte de dialogue modale a un arrière-plan festif&nbsp;!</p>
  </dialog>
  
  <dialog id="DIALOG_2" class="cp_dialog">
    <button autofocus onclick="closeModal('DIALOG_2')">Fermer</button>
    <p>Cette seconde boîte de dialogue modale n'est pas indépendante&nbsp;</p>
    <form id="form10" class="form_style1"  onsubmit="return validateForm(this)">

      <label for="fname">First Name</label>
      <input type="text" name="firstname" value="" pattern="[a-zA-Z]{2,50}" placeholder="Saisir le prénom" required minlength="2" maxlength="50"/>

      <label for="lname">Last Name</label>
      <input type="text" name="lastname" placeholder="Your last name..">

      <label for="country">Country</label>
      <select name="country">
        <option value="australia">Australia</option>
        <option value="canada">Canada</option>
        <option value="usa">USA</option>
      </select>
      
      <textarea name="message">Some text...</textarea>
      
      <input type="submit" value="Submit">
    
    </form>        
  </dialog>

-->  