# Misc : Welcome <3

`Given a hint `
![img](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/Welcome/img/hint.png)

`Checking Description`
![img](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/Welcome/img/disc.png)

* `welcome <3` is in description 
### cheking the source CODE
```htm
<div role="tabpanel" class="tab-pane fade show active" id="challenge">
						<h2 class="challenge-name text-center pt-3">Welcome &lt;3</h2>
						<h3 class="challenge-value text-center">100</h3>
						<div class="challenge-tags text-center">
							
						</div>
						<span class="challenge-desc"><p><a href="https://scoreboard.acebear.team">Free Flag</a>
<a style="color:white;font-size:1px;">}galF_eerF_yllaeR{raeBecA</a></p>
</span>
						<div class="challenge-hints hint-row row">
							
								<div class="col-md-12 hint-button-wrapper text-center mb-3">
									<a class="btn btn-info btn-hint btn-block" href="javascript:;" onclick="javascript:loadhint(2)">
										
											
												<small>
													View Hint
												</small>
											
										
									</a>
								</div>
							
						</div>
						<div class="row challenge-files text-center pb-3">
							
						</div>

						<div class="row submit-row">
							<div class="col-md-9 form-group">
								<input class="form-control" type="text" name="answer" id="submission-input" placeholder="Flag">
								<input id="challenge-id" type="hidden" value="13">
							</div>
							<div class="col-md-3 form-group key-submit">
								<button type="submit" id="submit-key" tabindex="5" class="btn btn-md btn-outline-secondary float-right">Submit
								</button>
							</div>
						</div>
						<div class="row notification-row">
							<div class="col-md-12">
								<div id="result-notification" class="alert alert-dismissable text-center w-100" role="alert" style="display: none;">
									<strong id="result-message"></strong>
								</div>
							</div>
						</div>
					</div>
```

* There it is `<a style="color:white;font-size:1px;">}galF_eerF_yllaeR{raeBecA</a>`

### flag is :: `AceBear{Really_Free_Flag}`